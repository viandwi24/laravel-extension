<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Viandwi24\LaravelExtension\Facades\Hook;

class HookListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hook:list
        {--a|--only-action : Only list action hook}
        {--f|--only-filter : Only list filter hook}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get List Hook Registered';

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
        if (
            !$this->option('only-action') 
            && !$this->option('only-filter')
        ) {
            $this->getActionList();
            $this->getFilterList();
        } elseif ($this->option('only-action')) {
            $this->getActionList();
        } elseif ($this->option('only-filter')) {
            $this->getFilterList();
        }
    }

    private function getActionList()
    {
        // action
        $actions = Hook::getAction();
        $this->question("[List Action Registered]\n");
        foreach($actions as $key => $action)
        {
            $this->comment("[*] {$key}");
            foreach($action as $item)
            {
                $this->info("    + {$item->extension} [{$item->priority}]");
            }
            $this->info('');
        }
    }

    private function getFilterList()
    {
        // filter
        $filters = Hook::getFilter();
        $this->question("[List Filter Registered]\n");
        foreach($filters as $key => $filter)
        {
            $this->comment("[*] {$key}");
            foreach($filter as $item)
            {
                $this->info("    + {$item->extension} [{$item->priority}]");
            }
            $this->info('');
        }
    }
}
