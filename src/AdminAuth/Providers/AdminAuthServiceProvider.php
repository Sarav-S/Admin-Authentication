<?php

namespace Sarav\AdminAuth\Providers;

use Illuminate\Support\ServiceProvider;
use Sarav\AdminAuth\Console\AdminAuth;

class AdminAuthServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthConfig();
        $this->registerAuthCommand();
        $this->publishAdminConfig();
    }

    /**
     * Registers the auth configuration
     * dynamically in config/auth.php file.
     */
    public function registerAuthConfig()
    {
    	$this->app['config']['auth.guards'] = ['admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ]];

        $this->app['config']['auth.providers'] = ['admins' => [
            'driver' => 'eloquent',
            'model' => \App\Admin::class,
        ]];

        $this->app['config']['auth.passwords'] = ['admins' => [
            'provider' => 'admins',
            'email'    => 'admin.auth.emails.password',
            'table'    => 'password_resets',
            'expire'   => 60,
        ]];
    }

    /**
     * Registers the admin:auth command
     */
    public function registerAuthCommand()
    {
    	$this->app->singleton('command.admin.auth', function () {
            return new AdminAuth();
        });
        
        $this->commands(['command.admin.auth']);
    }

    public function publishAdminConfig()
    {
    	$source = realpath(__DIR__.'/../config/admin.php');
        $this->publishes([$source => config_path('admin.php')]);
    }
}