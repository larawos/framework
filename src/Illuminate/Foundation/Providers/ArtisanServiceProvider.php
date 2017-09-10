<?php

namespace Larawos\Illuminate\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Larawos\Illuminate\Foundation\Console\ViewMakeCommand;
use Larawos\Illuminate\Foundation\Console\ModelMakeCommand;
use Larawos\Illuminate\Foundation\Console\RequestMakeCommand;
use Larawos\Illuminate\Foundation\Console\ResourceMakeCommand;
use Larawos\Illuminate\Foundation\Console\RepositoryMakeCommand;
use Larawos\Illuminate\Foundation\Console\LarawosInstallCommand;
use Larawos\Illuminate\Foundation\Console\ControllerMakeCommand;
use Larawos\Illuminate\Foundation\Console\NotificationMakeCommand;

class ArtisanServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'ViewMake'         => 'command.view.make',
        'ModelMake'        => 'command.model.make',
        'RequestMake'      => 'command.request.make',
        'ResourceMake'     => 'command.resource.make',
        'RepositoryMake'   => 'command.repository.make',
        'ControllerMake'   => 'command.controller.make',
        'NotificationMake' => 'command.notificaion.make',
        'LarawosInstall'   => 'command.larawos.install'
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands, 'local' == config('app.env') ? $this->devCommands : []
        ));
    }

    /**
     * Register the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], []);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerViewMakeCommand()
    {
        $this->app->singleton('command.view.make', function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRepositoryMakeCommand()
    {
        $this->app->singleton('command.repository.make', function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerConsoleMakeCommand()
    {
        $this->app->singleton('command.console.make', function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerLarawosInstallCommand()
    {
        $this->app->singleton('command.larawos.install', function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerControllerMakeCommand()
    {
        $this->app->singleton('command.controller.make', function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerModelMakeCommand()
    {
        $this->app->singleton('command.model.make', function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerNotificationMakeCommand()
    {
        $this->app->singleton('command.notification.make', function ($app) {
            return new NotificationMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerResourceMakeCommand()
    {
        $this->app->singleton('command.resource.make', function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), 'local' == config('app.env') ? array_values($this->devCommands) : []);
    }
}
