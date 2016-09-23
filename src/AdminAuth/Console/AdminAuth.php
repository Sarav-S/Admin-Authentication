<?php

namespace Sarav\AdminAuth\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\AppNamespaceDetectorTrait;

class AdminAuth extends Command
{
    use AppNamespaceDetectorTrait;
    
	/**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic login views and routes for admin user';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/login.stub'           => 'auth/login.blade.php',
        'auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
        'auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php',
        'auth/emails/password.stub' => 'auth/emails/password.blade.php',
        'layouts/app.stub'          => 'layouts/app.blade.php',
        'home.stub'                 => 'home.blade.php',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->createDirectories();

        $this->exportViews();

        $this->info('Installed HomeController.');

        file_put_contents(
            app_path('Http/Controllers/Admin/HomeController.php'),
            $this->compileControllerStub('HomeController.stub')
        );

        $this->info('Installed AuthController.');

        file_put_contents(
            app_path('Http/Controllers/Admin/AuthController.php'),
            $this->compileControllerStub('AuthController.stub')
        );

        $this->info('Installed PasswordController.php');

        file_put_contents(
            app_path('Http/Controllers/Admin/PasswordController.php'),
            $this->compileControllerStub('PasswordController.stub')
        );

        $this->info('Updated Routes File.');

        file_put_contents(
            app_path('Http/routes.php'),
            file_get_contents(__DIR__.'/stubs/admin/routes.stub'),
            FILE_APPEND
        );

        $this->info('Installed Middlewares');

        file_put_contents(
            app_path('Http/Middleware/AdminAuthenticate.php'),
            $this->compileMiddlewareStub('adminauthenticate.stub')
        );

        file_put_contents(
            app_path('Http/Middleware/AdminRedirectIfAuthenticated.php'),
            $this->compileMiddlewareStub('redirectifauthenticated.stub')
        );

        $this->info('Admin migrations table created');

        file_put_contents(
            database_path('migrations/2014_10_12_000000_create_admins_table.php'),
            file_get_contents(__DIR__.'/stubs/admin/migrations/2014_10_12_000000_create_admins_table.php')
        );

        $this->info('Admin model created');

        file_put_contents(
            app_path('Admin.php'),
            file_get_contents(__DIR__.'/stubs/admin/model/admin.stub')
        );

        app('composer')->dumpAutoloads();

        $this->info('Generating autoload files');

        $this->comment('Admin Authentication scaffolding generated successfully!');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir(base_path('resources/views/admin'))) {
            mkdir(base_path('resources/views/admin'), 0755, true);
        }

        if (! is_dir(base_path('resources/views/admin/layouts'))) {
            mkdir(base_path('resources/views/admin/layouts'), 0755, true);
        }

        if (! is_dir(base_path('resources/views/admin/auth/passwords'))) {
            mkdir(base_path('resources/views/admin/auth/passwords'), 0755, true);
        }

        if (! is_dir(base_path('resources/views/admin/auth/emails'))) {
            mkdir(base_path('resources/views/admin/auth/emails'), 0755, true);
        }

        if (! is_dir(app_path('Http/Controllers/Admin'))) {
            mkdir(app_path('Http/Controllers/Admin'), 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {

            $path = base_path('resources/views/admin/'.$value);

            $this->line('<info>Created View:</info> '.$path);

            copy(__DIR__.'/stubs/admin/views/'.$key, $path);
        }
    }

    /**
     * Compiles the Controllers stub.
     *
     * @return string
     */
    protected function compileControllerStub($stub)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/stubs/admin/controllers/'.$stub)
        );
    }

    /**
     * Compiles the Middleware stub.
     *
     * @return string
     */
    protected function compileMiddlewareStub($stub)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/stubs/admin/middlewares/'.$stub)
        );
    }
}