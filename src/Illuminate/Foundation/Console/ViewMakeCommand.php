<?php

namespace Larawos\Illuminate\Foundation\Console;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ViewMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the module views.';

    /**
     * Get the stub files for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return resource_path('stubs/view');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($rawName));
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $name = $this->qualifyClass($this->getNameInput());

        $names = [
            $name . '.index',
            $name . '.show',
            $name . '.create',
            $name . '.edit',
        ];

        foreach ($names as $key => $name) {
            $path = $this->getPath($name);

            // First we will check to see if the class already exists. If it does, we don't want
            // to create the class and overwrite the user's code. So, we will bail out so the
            // code is untouched. Otherwise, we will continue generating this class' files.
            if ($this->alreadyExists($name)) {
                $this->error($name.' already exists!');
                continue;
            }

            // Next, we will generate the path to the location where this class' file should get
            // written. Then, we will build the class and make the proper replacements on the
            // stub files so that it gets the correctly formatted namespace and class name.
            $this->makeDirectory($path);

            $this->files->put($path, $this->buildClass($name));

            $this->info($name.' created successfully.');
        }
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return trim($name, '.');
    }

    /**
     * Get the destination view path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return resource_path('views').'/'.str_replace('.', '/', $name).'.blade.php';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $subNameArray = explode('.', $name);
        $stub = $this->files->get($this->getStub() . '/' . array_pop($subNameArray) . '.blade.stub');

        return $stub;
    }
}
