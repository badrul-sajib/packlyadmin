<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register all module service providers from composer.json
     */
    public function register(): void
    {
        $this->registerModulesFromComposer();
    }

    /**
     * Boot module routes, views, and migrations
     */
    public function boot(): void
    {
        $this->loadModuleViews();
        $this->loadModuleMigrations();
        $this->publishModuleAssets();
    }

    /**
     * Register modules by reading composer.json in each module folder
     */
    protected function registerModulesFromComposer(): void
    {
        $modulePaths = [
            base_path('modules'),
            base_path('modules/Api'),
        ];

        foreach ($modulePaths as $basePath) {
            if (!is_dir($basePath)) continue;

            $this->scanAndRegisterModules($basePath);
        }
    }

    protected function scanAndRegisterModules(string $path): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $dir) {
            if (!$dir->isDir()) continue;

            $composerPath = $dir->getPathname() . '/composer.json';

            if (!file_exists($composerPath)) continue;

            $composer = json_decode(file_get_contents($composerPath), true);

            if (
                !$composer ||
                !isset($composer['extra']['laravel']['providers']) ||
                !is_array($composer['extra']['laravel']['providers'])
            ) {
                continue;
            }

            foreach ($composer['extra']['laravel']['providers'] as $providerClass) {
                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                }
            }
        }
    }

    /**
     * Load all web.php and api.php route files
     */
    protected function loadModuleRoutes(): void
    {
        $routeFiles = $this->findFilesRecursively(base_path('modules'), ['web.php', 'api.php']);

        foreach ($routeFiles as $file) {
            $this->loadRoutesFrom($file);
        }
    }

    /**
     * Load all module views with proper namespace
     */
    protected function loadModuleViews(): void
    {
        $viewDirs = $this->findDirectoriesRecursively(base_path('modules'), 'views');

        foreach ($viewDirs as $viewPath) {

            $modulePath = dirname($viewPath, 2);
            $relative = Str::after($modulePath, base_path('modules') . DIRECTORY_SEPARATOR);

            $namespace = 'modules.' . str_replace(
                ['/', '\\'],
                '.',
                strtolower($relative)
            );

            \Log::info(['viewPath' => $viewPath, 'namespace' => $namespace, 'modulePath' => $modulePath]);

            $this->loadViewsFrom($viewPath, $namespace);
        }
    }

    /**
     * Load all module migrations
     */
    protected function loadModuleMigrations(): void
    {
        $migrationDirs = $this->findDirectoriesRecursively(base_path('modules'), 'Migrations');

        foreach ($migrationDirs as $dir) {
            $this->loadMigrationsFrom($dir);
        }
    }

    /**
     * Optional: Publish public assets from modules
     */
    protected function publishModuleAssets(): void
    {
        $assetDirs = $this->findDirectoriesRecursively(base_path('modules'), 'Resources/assets');

        foreach ($assetDirs as $source) {
            $relative = Str::after($source, base_path('modules') . DIRECTORY_SEPARATOR);
            $parts = explode(DIRECTORY_SEPARATOR, $relative);
            $moduleName = $parts[0] . (isset($parts[1]) ? '/' . $parts[1] : ''); // e.g. Admin or Api/V1/Ecommerce

            $this->publishes([
                $source => public_path('modules/' . str_replace('/', '-', strtolower($moduleName))),
            ], 'module-assets');
        }
    }

    // ==================== HELPER METHODS ====================

    protected function findFilesRecursively(string $path, array $filenames): array
    {
        $found = [];

        if (!is_dir($path)) return $found;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getFilename(), $filenames)) {
                $found[] = $file->getPathname();
            }
        }

        return $found;
    }

    protected function findDirectoriesRecursively(string $path, string $folderName): array
    {
        $found = [];

        if (!is_dir($path)) return $found;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $dir) {
            if ($dir->isDir() && $dir->getFilename() === $folderName) {
                $found[] = $dir->getPathname();
            }
        }

        return $found;
    }
}
