<?php

namespace Viandwi24\LaravelExtension\Commands;

use Extension;
use Illuminate\Console\Command;

class ExtensionUpdateListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:update-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating list installed extension';

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
        $this->info("[*] updating list extension...");
        
        // updating
        try {
            $update = Extension::updateList();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

        // info
        $count = count($update["list"]);
        $this->info("[*] updating finish, `{$count}` extension listed");
    }
}
