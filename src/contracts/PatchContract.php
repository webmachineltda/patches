<?php

namespace Webmachine\Patches\Contracts;

use Illuminate\Console\Command;

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
     * Run
     *  
     * @return boolean
     */
    abstract public function run();
}