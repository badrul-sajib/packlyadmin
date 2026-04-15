<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class ModuleReader
{
    protected string $modulesPath;

    public function __construct()
    {
        $this->modulesPath = base_path('modules/Api/V1/Merchant');
    }

    /**
     * Get all modules with their metadata from composer.json files
     *
     * @return array
     */
    public function getAllModules(): array
    {
        $modules = [];

        if (!File::exists($this->modulesPath)) {
            return $modules;
        }

        $directories = File::directories($this->modulesPath);

        foreach ($directories as $directory) {
            $composerFile = $directory . '/composer.json';

            if (File::exists($composerFile)) {
                $composerData = json_decode(File::get($composerFile), true);

                if (isset($composerData['hide_module']) && $composerData['hide_module'] === true) {
                    continue;
                }

                if ($composerData) {
                    $moduleName = $composerData['name'] ?? basename($directory);

                    $modules[$moduleName] = [
                        'name' => $moduleName,
                        'description' => $composerData['description'] ?? '',
                        'group_name' => $composerData['group_name'] ?? 'Other',
                        'sub_group_name' => $composerData['sub_group_name'] ?? 'General',
                        'dependent_modules' => $composerData['dependent_modules'] ?? [],
                        'parent_folder' => $composerData['parent_folder'] ?? '',
                    ];
                }
            }
        }

        return $modules;
    }

    /**
     * Get modules grouped by group_name and sub_group_name
     *
     * @return array
     */
    public function getGroupedModules(): array
    {
        $modules = $this->getAllModules();
        $grouped = [];

        foreach ($modules as $module) {
            $groupName = $module['group_name'];
            $subGroupName = $module['sub_group_name'];

            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }

            // If sub_group_name is empty, add modules directly to the group without sub-grouping
            if (empty($subGroupName)) {
                if (!isset($grouped[$groupName]['_direct'])) {
                    $grouped[$groupName]['_direct'] = [];
                }
                $grouped[$groupName]['_direct'][] = $module;
            } else {
                // Normal sub-grouping
                if (!isset($grouped[$groupName][$subGroupName])) {
                    $grouped[$groupName][$subGroupName] = [];
                }
                $grouped[$groupName][$subGroupName][] = $module;
            }
        }

        return $grouped;
    }

    /**
     * Get dependency map for all modules
     *
     * @return array
     */
    public function getDependencyMap(): array
    {
        $modules = $this->getAllModules();
        $dependencyMap = [];

        foreach ($modules as $moduleName => $module) {
            $dependencyMap[$moduleName] = $module['dependent_modules'];
        }

        return $dependencyMap;
    }

    /**
     * Get reverse dependency map (which modules depend on each module)
     *
     * @return array
     */
    public function getReverseDependencyMap(): array
    {
        $modules = $this->getAllModules();
        $reverseDependencyMap = [];

        // Initialize all modules
        foreach ($modules as $moduleName => $module) {
            $reverseDependencyMap[$moduleName] = [];
        }

        // Build reverse dependencies
        foreach ($modules as $moduleName => $module) {
            foreach ($module['dependent_modules'] as $dependency) {
                if (isset($reverseDependencyMap[$dependency])) {
                    $reverseDependencyMap[$dependency][] = $moduleName;
                }
            }
        }

        return $reverseDependencyMap;
    }
}
