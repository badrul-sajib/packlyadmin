<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{

    protected $signature = 'make:module {path} {--api-version= : The API version (e.g. v1, v2)} {--group= : Custom group name for module:list} {--subgroup= : Custom sub-group name}';

    protected $description = 'Create a new module with proper nested structure and customizable grouping';

    protected Filesystem $files;
    protected ?string $apiVersion = null;
    protected bool $isApiModule = false;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $path = trim($this->argument('path'));
        $rawApiVersion = $this->option('api-version');

        if ($rawApiVersion) {
            $this->apiVersion = strtoupper(trim($rawApiVersion));
            $this->isApiModule = true;
            $this->info("Creating API module v{$this->apiVersion}: {$path}");
        } else {
            $this->info("Creating regular module: {$path}");
        }

        $directoryPath = $this->studlyPath($path);
        $moduleBasePath = $this->isApiModule
            ? base_path("modules/Api/{$this->apiVersion}/{$directoryPath}")
            : base_path("modules/{$directoryPath}");

        if ($this->files->exists($moduleBasePath)) {
            $this->error("Module already exists: {$moduleBasePath}");
            return 1;
        }

        $this->makeDirectory($moduleBasePath);
        $this->createModuleStructure($moduleBasePath, $path);

        $this->info("Module created successfully!");
        $this->warn("Path: {$moduleBasePath}");
        $this->warn("Route prefix: " . $this->getRoutePrefix($path));

        return 0;
    }

    // app/Console/Commands/MakeModuleCommand.php
    protected function createModuleStructure(string $modulePath, string $originalPath): void
    {
        $directories = [             // legacy – kept for web modules
            'Http/Controllers',
            'Http/Requests',
            'Http/Resources',
            'Models',
            'Routes',
            'Migrations',
            'Config',
            'Providers',
            'Repositories',
            'Repositories/Contracts',
            'Services',
            'Resources/assets',
        ];

        if (! $this->isApiModule) {
            $directories[] = 'Resources/views';
        }

        foreach ($directories as $dir) {
            $this->makeDirectory("{$modulePath}/{$dir}");
        }

        $moduleName = $this->getControllerName($originalPath);

        // Common files
        //create model
        // Common files
        $this->createModel($modulePath, $originalPath, $moduleName);
        $this->createServiceProvider($modulePath, $originalPath, $moduleName . 'ServiceProvider');
        $this->createRepositoryInterface($modulePath, $originalPath, $moduleName);
        $this->createRepository($modulePath, $originalPath, $moduleName);
        $this->createService($modulePath, $originalPath, $moduleName);
        $this->createFormRequests($modulePath, $originalPath, $moduleName);
        $this->createResource($modulePath, $originalPath, $moduleName);

        // === Controller & Routes ===
        if ($this->isApiModule) {
            $this->createApiController($modulePath, $originalPath, $moduleName);
        } else {
            $this->createWebController($modulePath, $originalPath, $moduleName);
            $this->createSampleViews($modulePath, $moduleName);
        }

        // Routes are created correctly already (web.php vs api.php)
        $this->createRoutes($modulePath, $originalPath, $moduleName . 'Controller');

        $this->createComposerJson($modulePath, $originalPath, $moduleName . 'ServiceProvider');
    }

    protected function createModel(string $modulePath, string $originalPath, string $moduleName): void
    {
        $namespace = $this->getNamespace($originalPath) . '\\Models';

        $stub = $this->files->get(__DIR__ . '/stubs/model.stub');

        $stub = str_replace(
            ['{{namespace}}', '{{modelName}}'],
            [$namespace, $moduleName],
            $stub
        );

        $this->files->put("{$modulePath}/Models/{$moduleName}.php", $stub);
        $this->info("Model created: {$moduleName}");
    }

    protected function createApiController(string $modulePath, string $path, string $moduleName): void
    {
        // Base module namespace (e.g. Modules\Ecommerce\Category)
        $baseNamespace = $this->getNamespace($path);

        // Correct namespaces
        $controllerNamespace = $baseNamespace . '\\Http\\Controllers';
        $serviceNamespace     = $baseNamespace . '\\Services';
        $requestNamespace     = $baseNamespace . '\\Http\\Requests';
        $resourceNamespace    = $baseNamespace . '\\Http\\Resources';

        $controllerName = $moduleName . 'Controller';
        $resourceName    = $moduleName . 'Resource';

        $stub = $this->files->get(__DIR__ . '/stubs/controller-api.stub');

        $stub = str_replace(
            [
                '{{controllerNamespace}}',
                '{{serviceNamespace}}',
                '{{requestNamespace}}',
                '{{resourceNamespace}}',
                '{{controllerName}}',
                '{{modelName}}',
                '{{resourceName}}',
            ],
            [
                $controllerNamespace,
                $serviceNamespace,
                $requestNamespace,
                $resourceNamespace,
                $controllerName,
                $moduleName,
                $resourceName,
            ],
            $stub
        );

        $this->files->put("{$modulePath}/Http/Controllers/{$controllerName}.php", $stub);
        $this->info("API Controller created: Http/Controllers/{$controllerName}.php");
    }

    protected function createRepositoryInterface(string $modulePath, string $path, string $moduleName): void
    {
        $namespace = $this->getNamespace($path) . '\\Repositories\\Contracts';
        $stub = $this->files->get(__DIR__ . '/stubs/repository-interface.stub');
        $stub = str_replace(['{{namespace}}', '{{interface}}', '{{model}}'], [$namespace, $moduleName . 'RepositoryInterface', $moduleName], $stub);
        $this->files->put("{$modulePath}/Repositories/Contracts/{$moduleName}RepositoryInterface.php", $stub);
    }

    protected function createRepository(string $modulePath, string $path, string $moduleName): void
    {
        $namespace = $this->getNamespace($path) . '\\Repositories';
        $interface = $moduleName . 'RepositoryInterface';
        $modelNs   = $this->getNamespace($path) . '\\Models';

        $stub = $this->files->get(__DIR__ . '/stubs/repository.stub');
        $stub = str_replace(
            ['{{namespace}}', '{{interface}}', '{{modelNamespace}}', '{{repository}}', '{{model}}'],
            [$namespace, $interface, $modelNs, $moduleName . 'Repository', $moduleName],
            $stub
        );

        $this->files->put("{$modulePath}/Repositories/{$moduleName}Repository.php", $stub);
    }

    protected function createService(string $modulePath, string $originalPath, string $moduleName): void
    {
        $namespace     = $this->getNamespace($originalPath); // e.g. Modules\Api\V1\User
        $serviceNs     = $namespace . '\\Services';
        $contractNs    = $namespace . '\\Repositories\\Contracts';

        $stub = $this->files->get(__DIR__ . '/stubs/service.stub');

        $stub = str_replace(
            [
                '{{namespace}}',
                '{{moduleName}}',
                '{{repositoryInterface}}',
            ],
            [
                $serviceNs,
                $moduleName,
                $contractNs . '\\' . $moduleName . 'RepositoryInterface',
            ],
            $stub
        );

        $this->files->put("{$modulePath}/Services/{$moduleName}Service.php", $stub);
        $this->info("Service created: {$moduleName}Service");
    }


    protected function createFormRequests(string $modulePath, string $path, string $moduleName): void
    {
        $namespace = $this->getNamespace($path) . '\\Http\\Requests';

        // Store request
        $stub = $this->files->get(__DIR__ . '/stubs/request-store.stub');
        $stub = str_replace(['{{namespace}}', '{{model}}'], [$namespace, $moduleName], $stub);
        $this->files->put("{$modulePath}/Http/Requests/Store{$moduleName}Request.php", $stub);

        // Update request
        $stub = $this->files->get(__DIR__ . '/stubs/request-update.stub');
        $stub = str_replace(['{{namespace}}', '{{model}}'], [$namespace, $moduleName], $stub);
        $this->files->put("{$modulePath}/Http/Requests/Update{$moduleName}Request.php", $stub);
    }

    protected function createResource(string $modulePath, string $path, string $moduleName): void
    {
        $namespace = $this->getNamespace($path) . '\\Http\\Resources';
        $stub = $this->files->get(__DIR__ . '/stubs/resource.stub');
        $stub = str_replace(['{{namespace}}', '{{resource}}', '{{model}}'], [$namespace, $moduleName . 'Resource', $moduleName], $stub);
        $this->files->put("{$modulePath}/Http/Resources/{$moduleName}Resource.php", $stub);
    }

    protected function createServiceProvider(string $modulePath, string $originalPath, string $providerClass): void
    {
        $namespace     = $this->getNamespace($originalPath);
        $moduleName    = $this->getControllerName($originalPath);
        $viewNamespace = $this->getRoutePrefix($originalPath);
        $configKey     = strtolower(str_replace(['\\', '/'], '.', $namespace));

        $stub = $this->files->get(__DIR__ . '/stubs/module-service-provider.stub');

        $stub = str_replace(
            [
                '{{namespace}}',
                '{{moduleName}}',
                '{{providerClass}}',
                '{{viewNamespace}}',
                '{{configKey}}',
            ],
            [
                $namespace,
                $moduleName,
                $providerClass,
                $viewNamespace,
                $configKey,
            ],
            $stub
        );

        $this->files->put("{$modulePath}/Providers/{$providerClass}.php", $stub);
        $this->info("Service Provider created → auto-bound repository & service");
    }

    protected function createRoutes(string $modulePath, string $path, string $controllerClass): void
    {
        $namespace = $this->getNamespace($path);
        $prefix    = $this->getRoutePrefix($path);
        $fullControllerNs = $namespace . '\\Http\\Controllers';

        if (! $this->isApiModule) {
            $stub = $this->files->exists(__DIR__ . '/stubs/routes-web.stub')
                ? $this->files->get(__DIR__ . '/stubs/routes-web.stub')
                : $this->getDefaultWebRouteStub();

            $stub = str_replace(
                [
                    '{{fullControllerNamespace}}',
                    '{{controller}}',
                    '{{prefix}}',
                ],
                [
                    $fullControllerNs,
                    $controllerClass,
                    $prefix,
                ],
                $stub
            );

            $this->files->put("{$modulePath}/Routes/web.php", $stub);
            $this->info("Web routes created: Routes/web.php");
        } else {
            $stub = $this->files->exists(__DIR__ . '/stubs/routes-api.stub')
                ? $this->files->get(__DIR__ . '/stubs/routes-api.stub')
                : $this->getDefaultApiRouteStub();

            $stub = str_replace(
                [
                    '{{apiVersion}}',
                    '{{prefix}}',
                    '{{fullControllerNamespace}}',
                    '{{controller}}',
                ],
                [
                    strtolower($this->apiVersion),
                    $prefix,
                    $fullControllerNs,
                    $controllerClass,
                ],
                $stub
            );

            $this->files->put("{$modulePath}/Routes/api.php", $stub);
            $this->info("API routes created: Routes/api.php");
        }
    }

    protected function createWebController(string $modulePath, string $path, string $moduleName): void
    {
        $baseNamespace     = $this->getNamespace($path);
        $controllerNs      = $baseNamespace . '\\Http\\Controllers';
        $serviceNs         = $baseNamespace . '\\Services';
        $requestNs         = $baseNamespace . '\\Http\\Requests';

        $viewPrefix        = $this->getRoutePrefix($path); // e.g. ecommerce/category
        $routePrefix       = str_replace('/', '.', $viewPrefix); // e.g. ecommerce.category

        $stub = $this->files->get(__DIR__ . '/stubs/controller-web.stub');

        $stub = str_replace(
            [
                '{{controllerNamespace}}',
                '{{serviceNamespace}}',
                '{{requestNamespace}}',
                '{{controllerName}}',
                '{{modelName}}',
                '{{viewPrefix}}',
                '{{routePrefix}}',
            ],
            [
                $controllerNs,
                $serviceNs,
                $requestNs,
                $moduleName . 'Controller',
                $moduleName,
                $viewPrefix,
                $routePrefix,
            ],
            $stub
        );

        $this->files->put("{$modulePath}/Http/Controllers/{$moduleName}Controller.php", $stub);
        $this->info("Web Controller created: Http/Controllers/{$moduleName}Controller.php");
    }

    protected function createSampleViews(string $modulePath, string $moduleName): void
    {
        $stub = $this->files->exists(__DIR__ . '/stubs/view.stub')
            ? $this->files->get(__DIR__ . '/stubs/view.stub')
            : "<h1>Welcome to {{$moduleName}} Module</h1>";

        $views = ['index.blade.php', 'create.blade.php', 'edit.blade.php', 'show.blade.php'];
        foreach ($views as $view) {
            $content = str_replace('{{ module }}', $moduleName, $stub);
            $this->files->put("{$modulePath}/Resources/views/{$view}", $content);
        }
    }

    protected function createComposerJson(string $modulePath, string $path, string $providerClass): void
    {
        $namespace = $this->getNamespace($path);
        $segments  = array_filter(explode('/', $path));

        // Parent folder (first segment)
        $parentFolder = count($segments) >= 1 ? $this->toTitleCase($segments[0]) : 'General';

        // Use CLI options first, then fallback to path
        $groupName = $this->option('group');
        if (empty($groupName) && count($segments) >= 2) {
            $groupName = $this->toTitleCase($segments[1]);
        }
        $groupName = $groupName ?: 'General';

        $subGroupName = $this->option('subgroup');
        if (empty($subGroupName) && count($segments) >= 3) {
            $subGroupName = $this->toTitleCase($segments[2]);
        }
        $subGroupName = $subGroupName ?: '';

        // Composer package name
        $kebabPath   = collect($segments)->map([Str::class, 'kebab'])->implode('-');
        $packageName = "modules/{$kebabPath}";

        $composer = [
            "name"           => $packageName,
            "description"    => "Laravel module: {$path}",
            "type"           => "laravel-module",
            "parent_folder"  => $parentFolder,
            "group_name"     => $groupName,
            "sub_group_name" => $subGroupName,
            "dependent_modules" => [],
            "load_module" => true,
            "hide_module" => false,
            "autoload" => [
                "psr-4" => [
                    $namespace . "\\" => "."
                ]
            ],
            "extra" => [
                "laravel" => [
                    "providers" => [
                        $namespace . "\\Providers\\" . $providerClass
                    ]
                ]
            ]
        ];

        $this->files->put(
            "{$modulePath}/composer.json",
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $display = "# {$parentFolder}";
        if ($groupName !== 'General') $display .= " → {$groupName}";
        if ($subGroupName) $display .= " → {$subGroupName}";

        $this->info("Created module in section: <fg=cyan>{$display}</>");
    }

    protected function toTitleCase(string $value): string
    {
        return Str::studly(str_replace(['-', '_'], ' ', $value));
    }

    // ==================== STUB FALLBACKS ====================

    protected function getDefaultWebRouteStub(): string
    {
        return <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use {{namespace}}\Controllers\{{controller}};

Route::prefix('{{prefix}}')
     ->name('{{prefix}}.')->group(function () {
    Route::get('/', [{{controller}}::class, 'index'])->name('index');
    Route::get('/create', [{{controller}}::class, 'create'])->name('create');
    Route::post('/', [{{controller}}::class, 'store'])->name('store');
    Route::get('/{id}', [{{controller}}::class, 'show'])->name('show');
    Route::get('/{id}/edit', [{{controller}}::class, 'edit'])->name('edit');
    Route::put('/{id}', [{{controller}}::class, 'update'])->name('update');
    Route::delete('/{id}', [{{controller}}::class, 'destroy'])->name('destroy');
});
PHP;
    }

    protected function getDefaultApiRouteStub(): string
    {
        return <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use {{namespace}}\Controllers\{{controller}};

Route::prefix('api/{{apiVersion}}/{{prefix}}')
     ->name('api.{{prefix}}.')->group(function () {
    Route::get('/', [{{controller}}::class, 'index']);
    Route::post('/', [{{controller}}::class, 'store']);
    Route::get('/{id}', [{{controller}}::class, 'show']);
    Route::put('/{id}', [{{controller}}::class, 'update']);
    Route::patch('/{id}', [{{controller}}::class, 'update']);
    Route::delete('/{id}', [{{controller}}::class, 'destroy']);
});
PHP;
    }

    // ==================== HELPERS ====================

    protected function studlyPath(string $path): string
    {
        return collect(explode('/', $path))
            ->map([Str::class, 'studly'])
            ->implode('/');
    }

    protected function getNamespace(string $path): string
    {
        $base = $this->isApiModule ? "Modules\\Api\\{$this->apiVersion}" : "Modules";

        $segments = collect(explode('/', $path))
            ->map([Str::class, 'studly'])
            ->implode('\\');

        return $base . '\\' . $segments;
    }

    protected function getRoutePrefix(string $path): string
    {
        return collect(explode('/', $path))
            ->map([Str::class, 'kebab'])
            ->filter()
            ->implode('/');
    }

    protected function getControllerName(string $path): string
    {
        $segments = explode('/', $path);
        return Str::studly(end($segments));
    }

    protected function makeDirectory(string $path): void
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }
}
