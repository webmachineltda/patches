<?php
namespace Webmachine\Patches;

use Illuminate\Support\ServiceProvider;
use Webmachine\Patches\Commands\PatchMakeCommand;
use Webmachine\Patches\Commands\PatchCommand;
use Webmachine\Patches\Commands\PatchLogCommand;

class PatchesServiceProvider extends ServiceProvider {
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        if (! class_exists('CreatePatchLogsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/database/migrations/create_patch_logs_table.php.stub' => database_path("migrations/{$timestamp}_create_patch_logs_table.php"),
            ], 'migrations');
        }
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                PatchMakeCommand::class,
                PatchCommand::class,
                PatchLogCommand::class,
            ]);
        }
    }
}