<?php

namespace Webmachine\Patches\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Webmachine\Patches\Models\PatchLog;

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
    protected $description = 'Patch';

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
        
        if(class_exists($patch_classpath) && $patch_class != 'Patch') {
            if (! $this->confirmToProceed()) {
                return;
            }
            if($this->wasRunned($patch_class) && !$this->option('force')) {
                $this->error("$patch_class Patch was already runned!");
                return;
            }
            $patch = new $patch_classpath($this);
            $this->alert("Running $patch_class Patch");
            if($patch->run()) {
                // save patch log
                $this->log($patch);
            } else {
                $this->error("$patch_class Patch Error");
            }
        } else {
            $this->error("$patch_class Patch Not Found");
            return;
        }
    }

    /**
     * Patch Log
     * 
     * @param Database\Patches\Patch $patch
     * @return void
     */
    protected function log($patch)
    {
        $this->info($patch->comment);
        PatchLog::create([
            'patch' => str_replace('Database\Patches\\', '', get_class($patch)),
            'comment' => $patch->comment
        ]);
    }

    /**
     * Checks if a patch was previously runned
     * 
     * @param string $patch_class
     * @return boolean
     */
    protected function wasRunned($patch_class)
    {
        $n_runs = PatchLog::where('patch', $patch_class)->count();
        return $n_runs > 0;
    }
}
