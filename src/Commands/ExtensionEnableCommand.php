<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Viandwi24\LaravelExtension\Exceptions\ExtensionException;
use Viandwi24\LaravelExtension\Facades\Extension;

class ExtensionEnableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:enable {name : Extension code name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a extension';

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
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        
        // enabling
        try {
            $enabled = Extension::enable($name);
        } catch (\Exception $th) {
            if ($th instanceof ExtensionException) $this->error($th->getMessage());
            exit;
        }

        // info
        $this->info("[*] Enable extension `{$name}`");
    }
}
