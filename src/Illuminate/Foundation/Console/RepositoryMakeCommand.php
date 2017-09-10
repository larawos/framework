<?php

namespace Larawos\Illuminate\Foundation\Console;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = $this->parseModel($this->option('model'));

        if (empty(trim($model, $this->laravel->getNamespace().'Models'))) {
            $model = $this->parseModel($this->ask('witch model do you want to injected to the repository?'));
        }

        if (! class_exists($model)) {
            if ($this->confirm("A {$model} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $model]);
            }
        }

        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->replaceModelClass($stub, $model)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceModelClass(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyFullModelClass', 'DummyModelClass', 'DummyModelVariable'],
            [$name, class_basename($name), lcfirst(class_basename($name))],
            $stub
        );

        return $this;
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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a repository for the given model.'],
        ];
    }
}
