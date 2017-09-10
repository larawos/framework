<?php

namespace Larawos\Illuminate\Foundation\Console;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('view')) {
            return __DIR__.'/stubs/controller/controller.view.stub';
        }

        return __DIR__.'/stubs/controller/controller.api.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('view')) {
            $replace = $this->buildViewReplacements();
        } else {
            $replace = $this->buildRequestReplacements();
        }

        $replace = $this->buildResourceReplacements($replace);
        $replace = $this->buildModelReplacements($replace);
        $replace = $this->buildRepositoryReplacements($replace);

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    protected function buildViewReplacements()
    {
        $viewModule = $this->parseViewModule($this->option('view'));

        if (empty($viewModule)) {
            $viewModule = $this->parseViewModule($this->ask('witch module\'s view do you want to injected to the controller?'));
        }

        $views = $this->getViewModuleFiles($viewModule);

        foreach ($views as $view) {
            if (! file_exists($view.'.blade.php')) {
                if ($this->confirm("The {$viewModule} module has some views does not exist. Do you want to generate it?", true)) {
                    $this->call('make:view', ['name' => $viewModule]);
                }

                break;
            }
        }

        return [
            'DummyRootView' => $viewModule,
        ];
    }

    /**
     * Build the request replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRequestReplacements()
    {
        $name = $this->getNameInput();
        $rootNamespace = trim(
                $this->laravel->getNamespace() . 'Http\Requests' . '\\' . $this->getNamespace($name),
                '\\'
            );
        $storeRequest = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'StoreRequest';
        $updateRequest = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'UpdateRequest';

        if (! class_exists($storeRequest)) {
            if ($this->confirm("A {$storeRequest} request does not exist. Do you want to generate it?", true)) {
                $this->call('make:request', ['name' => $storeRequest]);
            }
        }

        if (! class_exists($updateRequest)) {
            if ($this->confirm("A {$updateRequest} request does not exist. Do you want to generate it?", true)) {
                $this->call('make:request', ['name' => $updateRequest]);
            }
        }

        return [
            'DummyFullStoreRequestClass' => $storeRequest,
            'DummyStoreRequestClass' => class_basename($storeRequest),
            'DummyFullUpdateRequestClass' => $updateRequest,
            'DummyUpdateRequestClass' => class_basename($updateRequest),
        ];
    }

    /**
     * Build the resource replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildResourceReplacements(array $replace)
    {
        $name = $this->getNameInput();
        $rootNamespace = trim(
                $this->laravel->getNamespace() . 'Http\Resources' . '\\' . $this->getNamespace($name),
                '\\'
            );
        $indexResource = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'IndexResource';
        $showResource = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'ShowResource';
        $storeResource = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'StoreResource';
        $updateResource = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'UpdateResource';
        $deleteResource = $rootNamespace . '\\' . str_replace('Controller', '', class_basename($name)) . 'DeleteResource';

        if (! class_exists($indexResource)) {
            if ($this->confirm("A {$indexResource} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => $indexResource, '--collection' => true]);
            }
        }

        if (! class_exists($showResource)) {
            if ($this->confirm("A {$showResource} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => $showResource]);
            }
        }

        if (! class_exists($storeResource)) {
            if ($this->confirm("A {$storeResource} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => $storeResource]);
            }
        }

        if (! class_exists($updateResource)) {
            if ($this->confirm("A {$updateResource} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => $updateResource]);
            }
        }

        if (! class_exists($deleteResource)) {
            if ($this->confirm("A {$deleteResource} resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => $deleteResource]);
            }
        }

        return array_merge($replace, [
            'DummyFullIndexResourceClass' => $indexResource,
            'DummyIndexResourceClass' => class_basename($indexResource),
            'DummyFullShowResourceClass' => $showResource,
            'DummyShowResourceClass' => class_basename($showResource),
            'DummyFullStoreResourceClass' => $storeResource,
            'DummyStoreResourceClass' => class_basename($storeResource),
            'DummyFullUpdateResourceClass' => $updateResource,
            'DummyUpdateResourceClass' => class_basename($updateResource),
            'DummyFullDeleteResourceClass' => $deleteResource,
            'DummyDeleteResourceClass' => class_basename($deleteResource),
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $this->modelClass = $this->option('model');

        $model = $this->parseModel($this->modelClass);

        if (empty(trim($model, $this->laravel->getNamespace().'Models'))) {
            $this->modelClass = $this->ask('witch model do you want to injected to the controller?');
            $model = $this->parseModel($this->modelClass);
        }

        if (! class_exists($model)) {
            if ($this->confirm("A {$model} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $model]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $model,
            'DummyModelClass' => class_basename($model),
            'DummyModelVariable' => snake_case(class_basename($model)),
            'DummyModelList' => str_plural(snake_case(class_basename($model))),
        ]);
    }

    /**
     * Build the repository replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRepositoryReplacements(array $replace)
    {
        $repository = $this->parseRepository($this->option('repository'));

        if (empty(trim($repository, $this->laravel->getNamespace(). 'Repositories\\'))) {
            $repository = $this->parseRepository($this->ask('witch repository do you want to injected to the controller?'));
        }

        if (! class_exists($repository)) {
            if ($this->confirm("A {$repository} repository does not exist. Do you want to generate it?", true)) {
                $this->call('make:repository', [
                        'name' => $repository,
                        '--model' => $this->modelClass
                    ]);
            }
        }

        return array_merge($replace, [
            'DummyFullRepositoryClass' => $repository,
            'DummyRepositoryClass' => class_basename($repository),
            'DummyRepositoryVariable' => lcfirst(class_basename($repository)),
        ]);
    }

    /**
     * Get the fully-qualified repository class name.
     *
     * @param  string  $repository
     * @return string
     */
    protected function parseRepository($repository)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $repository)) {
            throw new InvalidArgumentException('Repository name contains invalid characters.');
        }

        $repository = trim(str_replace('/', '\\', $repository), '\\');

        if (! Str::startsWith($repository, $rootNamespace = $this->laravel->getNamespace())) {
            $repository = $rootNamespace.'Repositories'.'\\'.$repository;
        }

        return $repository;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.'Models'.'\\'.$model;
        }

        return $model;
    }

    /**
     * Get the fully-qualified views name.
     *
     * @param  string  $view
     * @return string
     */
    protected function parseViewModule($view)
    {
        if (preg_match('([^A-Za-z0-9\-\.])', $view)) {
            throw new InvalidArgumentException('View name contains invalid characters.');
        }

        $view = trim($view, '.');

        return $view;
    }

    /**
     * Get the view files.
     *
     * @param  string  $view
     * @return array
     */
    protected function getViewModuleFiles($view)
    {
        $rootPath = resource_path('views/' . str_replace('.', '/', $view));

        return [
            $rootPath . '/index',
            $rootPath . '/show',
            $rootPath . '/create',
            $rootPath . '/edit'
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Inject a model to the controller.'],
            ['repository', 'r', InputOption::VALUE_OPTIONAL, 'Inject a repository to the controller.'],
            ['view', '', InputOption::VALUE_OPTIONAL, 'Generate a view controller class.'],
        ];
    }
}
