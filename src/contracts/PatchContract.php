<?php

namespace Webmachine\Patches\Contracts;

use Illuminate\Console\Command;
use Webmachine\Patches\Models\PatchLog;

abstract class PatchContract
{   
    /**
     * Command instance
     */
    public $command;

    /**
     * Log comment
     */
    public $comment;

    /**
     * Constructor
     * @param Illuminate\Console\Command $command 
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->comment = '';
    }

    /**
     * Determine if the patch is authorized to run.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Run
     *  
     * @return boolean
     */
    abstract public function run();

    /**
     * Checks if a patch was previously runned
     * 
     * @return boolean
     */
    public function wasRunned()
    {
        $n_runs = PatchLog::where('patch', $this->getFileName())->count();
        return $n_runs > 0;
    }

    /**
     * Patch Log
     * 
     * @return void
     */
    public function log()
    {
        $this->command->info($this->comment);
        PatchLog::create([
            'patch' => $this->getFileName(),
            'comment' => $this->comment
        ]);
    }
    
    /**
     * Get Patch FileName
     * 
     * @return string
     */
    protected function getFileName()
    {
        $reflector = new \ReflectionClass(get_class($this));
        return basename($reflector->getFileName());
    }
}