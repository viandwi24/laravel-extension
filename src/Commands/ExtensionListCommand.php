<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Viandwi24\LaravelExtension\Extension;

class ExtensionListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get List Installed Extension';

    protected $config = [];

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
        $exts = new Extension($this->config['path'], $this->config['debug']);
        $loaded = $exts->load();
        $this->question("[List Installed Extension]\n");
        foreach($loaded as $ext)
        {
            $this->comment("[*] " . $ext->config->name);
            $this->info("    " . $ext->config->description);
            $this->info(
                "    [Registered : " . ($ext->registered ? "Yes" : "Failed") . "]"
            );
            $this->info('');
        }
    }
}
