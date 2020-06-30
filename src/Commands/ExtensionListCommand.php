<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Viandwi24\LaravelExtension\Facades\Extension;

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
        $loaded = $this->getListExtension();
        $this->question("[List Installed Extension]\n");
        foreach($loaded as $ext)
        {
            $author = [
                "name" => (!$ext->config->author->name) ? "-" : $ext->config->author->name,
                "site" => (!$ext->config->author->site) ? "-" : $ext->config->author->site
            ];
            $active = ($ext->active) ? "True" : "False";

            $this->comment("[*] " . $ext->name);
            $this->info("    [Name] \t: " . $ext->config->name);
            $this->info("    [Descr] \t: " . $ext->config->description);
            $this->info("    [Version] \t: " . $ext->config->version);
            $this->info("    [Author] \t: {$author['name']} [{$author['site']}]");
            $this->info("    [Active] \t: {$active}");
            $this->info('');
        }
    }

    private function getListExtension()
    {
        $booted = app()->make('extension.booted');
        return $booted;
    }
}
