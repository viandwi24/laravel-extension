<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtensionInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate `Extension` folder and `extension.json`';

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
        $path = $this->config['path'];
        
        // make extension folder
        $this->info("[*] Generating folder in `{$path}`...");
        File::makeDirectory($path, 0777);

        // make configuration file extension.json
        $config_path = $path . "/" . "extension.json";
        $config = [ "list" => [], "active" => [] ];
        $this->info("[*] Generating config in `{$config_path}`...");
        file_put_contents(
            $config_path,
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }
}
