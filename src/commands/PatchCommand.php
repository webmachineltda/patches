<?php

namespace Webmachine\Patches\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class PatchCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch {patch_class}
                                {--f|force : Force the operation to run when in production or in case of repatch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a Patch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {      
        $patch_class = $this->argument('patch_class');
        $patch_classpath = "\Database\Patches\\$patch_class";
        
        if(class_exists($patch_classpath)) {
            $patch = new $patch_classpath($this);
            if (! $this->confirmToProceed()) {
                return;
            }
            if($patch->wasRunned() && !$this->option('force')) {
                $this->error("$patch_class Patch was already runned!");
                return;
            }
            $this->alert("Running $patch_class Patch");
            if($patch->run()) {
                // save patch log
                $patch->log();
            } else {
                $this->error("$patch_class Patch Error");
            }
        } else {
            $this->error("$patch_class Patch Not Found");
            return;
        }
    }
}
