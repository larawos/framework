<?php

namespace Larawos\Illuminate\Foundation\Console;

use Illuminate\Console\Command;

class LarawosInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larawos:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Larawos Framework.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $install = [];

        $install['APP_NAME'] = $this->ask('What\'s your project name?', 'Larawos');

        if ($dbsetting = $this->confirm('Do you wish to set your database right now? [y|N]')) {
            $install['DB_CONNECTION'] = $this->choice('What\'s your database connection? (default: mysql)', ['sqlite', 'mysql', 'pgsql', 'sqlsrv'], 1);
            $install['DB_DATABASE'] = $this->ask('What\'s your database name?');
            $install['DB_HOST'] = $this->ask('What\'s your database host?');
            $install['DB_USERNAME'] = $this->ask('What\'s your database user name?');
            $install['DB_PASSWORD'] = $this->secret('What\'s your database user password?');
            $install['DB_PREFIX'] = $this->ask('What\'s your database table prefix?');
        }

        foreach ($install as $key => $val) {
            $this->writeNewEnvironmentFileWith($key, $val);
        }

        if ($dbsetting) {
            $this->call('migrate', ['--seed' => null]);
        }

        $this->removeInstallLarawosCommand();

        $this->info("{$install['APP_NAME']} installed successfully.");
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key, $name)
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->replacementPattern($key),
            "{$key}={$name}\n",
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function replacementPattern($key)
    {
        return "/^{$key}=.*?\n/m";
    }

    public function removeInstallLarawosCommand()
    {
        file_put_contents(app_path('Console/Kernel.php'), str_replace(
            'Commands\LarawosInstallCommand::class,',
            '//',
            file_get_contents(app_path('Console/Kernel.php'))
        ));
    }
}
