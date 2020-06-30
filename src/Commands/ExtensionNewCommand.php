<?php

namespace Viandwi24\LaravelExtension\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtensionNewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:new {name : extension code name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create new extension';

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
        $name = $this->argument('name');
        $confirm = false;        
        do {
            $this->comment("[*] Generating config file [1/2]...");
            $config = $this->askConfig($name);
            $this->info("Your Namespace : `Extension\{$name}`");
            $this->info(json_encode($config, JSON_PRETTY_PRINT));
            $confirm = $this->confirm("Confirm?", true);
        } while (!$confirm);

        // 
        $path = $this->config['path'];
        $this->comment("[*] Generating folder extension `{$path}/{$name}`...\n");
        $mkdir = File::makeDirectory("{$path}/{$name}");
        $this->comment("[*] Generating config file `{$path}/{$name}/extension.json` [2/2]...\n");
        $mkconfig = $this->makeConfigFile("{$path}/{$name}/extension.json", $config);
        $this->comment("[*] Generating provider file `{$path}/{$name}/{$name}ServiceProvider.php` ...\n");
        $mkprovider = $this->makeProviderFile("{$path}/{$name}/{$name}ServiceProvider.php", $name);
    }

    /**
     * Ask configuration to user
     *
     * @param string $name
     * @return array
     */
    private function askConfig(string $name) : array
    {
        $config['name'] = $this->ask("Name : ", "Example Extension");
        $config['description'] = $this->ask("Description : ", "a Example Extension Description.");
        $config['version'] = $this->ask("Version : ", "1.0.0");
        $config['author']['name'] = $this->ask("Your Name : ", "yourname");
        $config['author']['site'] = $this->ask("Your Site : ", "mysite.com");
        $config['provider'] = "{$name}ServiceProvider";
        return $config;
    }

    /**
     * Make config file
     *
     * @param string $path
     * @param array $config
     * @return mixed
     */
    private function makeConfigFile(string $path, array $config)
    {
        $config = (object) json_decode(json_encode($config));
        $config_json = json_encode($config, JSON_PRETTY_PRINT);
        $write = file_put_contents($path, $config_json);
        return $write;
    }

    /**
     * Make Service Provider file
     *
     * @param string $path
     * @return void
     */
    private function makeProviderFile(string $path, $name)
    {
        $name = "{$name}ServiceProvider";
        $namespace = "Extension\\{$name}";
        $template = str_replace(
            [ '{{providerName}}', '{{providerNamespace}}', ], 
            [ $name, $namespace ], 
            $this->stub('ServiceProvider')
        );
        return file_put_contents($path , $template);
    }

    /**
     * get stub
     *
     * @param string $name
     * @return string
     */
    protected function stub(string $name) : string
    {
        return file_get_contents(__DIR__ . '/../../stubs/'. $name .'.stub');
    }
}
