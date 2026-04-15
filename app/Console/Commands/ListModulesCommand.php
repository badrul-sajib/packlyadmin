<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ListModulesCommand extends Command
{
    protected $signature = 'module:list
        {--show-dependencies : Show dependent modules for each module}
        {--only-ungrouped : Show only modules without any group}
        {--only-grouped : Show only grouped modules}';

    protected $description = 'List all Laravel modules with clean grouping and beautiful tree output';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $modules = $this->discoverModules();

        if (empty($modules)) {
            $this->warn('No modules found in modules/ or modules/Api/ directories.');
            return 0;
        }

        // Filter based on options
        if ($this->option('only-ungrouped')) {
            $modules = array_filter($modules, fn($m) => empty($m['parent_folder']) && empty($m['group_name']));
        } elseif ($this->option('only-grouped')) {
            $modules = array_filter($modules, fn($m) => !empty($m['parent_folder']) || !empty($m['group_name']));
        }

        $this->displayModules($modules);

        return 0;
    }

    protected function discoverModules(): array
    {
        $modules = [];
        $basePaths = [
            base_path('modules'),
            base_path('modules/Api'),
        ];

        foreach ($basePaths as $basePath) {
            if (!is_dir($basePath)) continue;
            $this->scanDirectory($basePath, $modules);
        }

        return $modules;
    }

    protected function scanDirectory(string $path, array &$modules): void
    {
        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                $composerPath = $fullPath . DIRECTORY_SEPARATOR . 'composer.json';

                if (file_exists($composerPath)) {
                    $composerData = json_decode(file_get_contents($composerPath), true);

                    if ($composerData && isset($composerData['extra']['laravel']['providers'])) {
                        $modules[] = [
                            'path'           => $fullPath,
                            'display_name'   => Str::studly(basename($fullPath)), // ← Clean name: Brand, Product, etc.
                            'composer_name'  => $composerData['name'] ?? basename($fullPath),
                            'description'    => $composerData['description'] ?? 'No description',
                            'parent_folder'  => $composerData['parent_folder'] ?? $this->guessParentFolder($fullPath),
                            'group_name'     => $composerData['group_name'] ?? '',
                            'sub_group_name' => $composerData['sub_group_name'] ?? '',
                            'dependent_modules' => $composerData['dependent_modules'] ?? [],
                            'type'           => str_contains($fullPath, 'modules/Api') ? 'API' : 'Web',
                            'version_folder' => $this->extractApiVersion($fullPath),
                        ];
                    }
                } else {
                    // Recurse deeper
                    $this->scanDirectory($fullPath, $modules);
                }
            }
        }
    }

    protected function extractApiVersion(string $path): ?string
    {
        if (preg_match('#modules/Api/([^/]+)/#i', $path, $matches)) {
            return strtoupper($matches[1]);
        }
        return null;
    }

    protected function guessParentFolder(string $path): string
    {
        if (preg_match('#modules/(?:Api/[A-Z0-9]+/)?([^/]+)/#i', $path, $m)) {
            return Str::studly($m[1]);
        }
        return 'General';
    }

    protected function displayModules(array $modules): void
    {
        // Group by parent_folder
        $grouped = [];
        foreach ($modules as $module) {
            $parent = $module['parent_folder'] ?? 'General';
            $grouped[$parent][] = $module;
        }

        ksort($grouped);

        $this->newLine();
        $this->line('<options=bold;fg=cyan>Laravel Modules Overview</>');
        $this->newLine();
        $this->info(" Total Modules: " . count($modules));
        $this->newLine();

        foreach ($grouped as $parentFolder => $mods) {
            $this->line("<options=bold;fg=yellow># {$parentFolder}</>");
            $this->newLine();

            // Split Web and API
            $webModules = array_filter($mods, fn($m) => $m['type'] === 'Web');
            $apiModules = array_filter($mods, fn($m) => $m['type'] === 'API');

            if ($webModules) {
                $this->line("  <fg=cyan>  ┌─ Web Modules</>");
                $this->renderModuleTree($webModules, "    ");
                $this->newLine();
            }

            if ($apiModules) {
                $byVersion = [];
                foreach ($apiModules as $m) {
                    $v = $m['version_folder'] ?? 'unknown';
                    $byVersion[$v][] = $m;
                }
                ksort($byVersion);

                foreach ($byVersion as $version => $list) {
                    $label = $version && $version !== 'unknown' ? "API v" . strtolower($version) : "API";
                    $this->line("  <fg=magenta>{$label} Modules</>");
                    $this->renderModuleTree($list, "    ");
                    $this->newLine();
                }
            }

            $this->newLine();
        }
    }

protected function renderModuleTree(array $modules, string $baseIndent = '    '): void
{
    $tree = [];

    foreach ($modules as $m) {
        $group    = $m['group_name'] ?: 'General';
        $subgroup = $m['sub_group_name'] ?: '';
        $tree[$group][$subgroup][] = $m;
    }

    $groups     = array_keys($tree);
    $groupCount = count($groups);

    foreach ($groups as $gi => $groupName) {
        $isLastGroup = ($gi === $groupCount - 1);
        $groupPrefix = $isLastGroup ? ' └' : ' ├';

        $this->line("{$baseIndent}{$groupPrefix}─ <fg=green>{$groupName}</>");

        $subgroups     = $tree[$groupName];
        $subgroupKeys  = array_keys($subgroups);
        $subgroupCount = count($subgroupKeys);

        foreach ($subgroupKeys as $si => $subgroupName) {
            $isLastSubgroup = ($si === $subgroupCount - 1);
            $subPrefix      = $isLastGroup ? ' ' : '│';
            $subLine        = $isLastSubgroup ? '└' : '├';

            $subIndent = "{$baseIndent} {$subPrefix}   ";

            if ($subgroupName !== '') {
                $this->line("{$subIndent}{$subLine}─ <fg=blue>{$subgroupName}</>");
                $moduleIndent = "{$subIndent}" . ($isLastSubgroup ? ' ' : '│') . '   ';
            } else {
                $moduleIndent = $subIndent;
            }

            $moduleList  = $subgroups[$subgroupName];
            $moduleCount = count($moduleList);

            foreach ($moduleList as $mi => $module) {
                $isLastModule = ($mi === $moduleCount - 1);
                $modPrefix    = $isLastModule ? '└' : '├';

                $name = $module['display_name'];

                $line = "{$moduleIndent}{$modPrefix}─ <options=bold>{$name}</>";

                if (!empty($module['description']) && $module['description'] !== 'No description') {
                    $desc = Str::limit($module['description'], 60);
                    $line .= " <comment>// {$desc}</comment>";
                }

                $this->line($line);
            }
        }
    }
}
}
