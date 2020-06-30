<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;

class ExtensionInspectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:inspect {name : Extension code name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect a extension with pretty json display.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = config('extension');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loaded = app()->make('extension.booted');
        $search = array_search(
            $this->argument('name'),
            (array_column($loaded, 'name'))
        );

        if ($search === false) {
            $this->error("Extension `{$this->argument('name')}` not found in list.");
            exit;
        } else {
            dd( $loaded[$search] );
        }
    }
}
