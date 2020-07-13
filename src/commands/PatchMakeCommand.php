<?php

namespace Webmachine\Patches\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PatchMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:patch {name : The name of the patch.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = trim($this->input->getArgument('name'));
        $this->writePatch($name);
        $this->composer->dumpAutoloads();
    }

    /**
     * Write the patch file to disk.
     *
     * @param  string  $name
     * @return string
     */
    protected function writePatch($name)
    {
        $this->ensurePatchDoesntAlreadyExist($name);

        $patches_dir = base_path() . '/database/patches';
        if (!is_dir($patches_dir)) {
            // dir doesn't exist, make it
            mkdir($patches_dir);
        }

        $stub = $this->files->get(__DIR__ . '/../database/patches/blank.stub');
        $file = $this->getClassName($name) . '.php';
        $this->files->put(
            $patches_dir . '/' . $file,
            $this->populateStub($name, $stub)
        );

        $this->line("<info>Created Patch:</info> {$file}");
    }

    /**
     * Ensure that a patch with the given name doesn't already exist.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensurePatchDoesntAlreadyExist($name)
    {
        if (class_exists($className = 'Database\Patches\\' . $this->getClassName($name))) {
            throw new InvalidArgumentException("A {$className} patch already exists.");
        }
    }

    /**
     * Get the class name of a patch name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Populate the place-holders in the patch stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @return string
     */
    protected function populateStub($name, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);
        return $stub;
    }
}
