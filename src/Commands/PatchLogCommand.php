<?php

namespace Webmachine\Patches\Commands;

use Illuminate\Console\Command;
use Webmachine\Patches\Models\PatchLog;

class PatchLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patch:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the Patch log';

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
        $headers = ['Patch', 'Comment', 'Run Date'];
        $patch_logs = PatchLog::all(['patch', 'comment', 'created_at'])->toArray();

        if (count($patch_logs) > 0) {
            $this->table($headers, $patch_logs);
        } else {
            $this->error('No Patch log found');
        }
    }
}
