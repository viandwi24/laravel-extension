<?php

namespace Viandwi24\LaravelExtension;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Viandwi24\LaravelExtension\Exceptions\NotFoundExtensionException;
use Viandwi24\LaravelExtension\Exceptions\ExtensionException;
use Viandwi24\LaravelExtension\Exceptions\NotFoundExtensionConfigException;
use Viandwi24\LaravelExtension\Exceptions\NotFoundExtensionGlobalConfigException;
use Viandwi24\LaravelExtension\Extension\WrapperServiceProvider;

class Extension
{
    private string $path = '';
    private bool $debug = false;
    private array $config = [];
    private array $loaded = [];
    private array $booted = [];

    /**
     * Constructor
     *
     * @param string $path Extension Base Path Location
     */
    public function __construct(string $path = '', bool $debug = false)
    {
        // path
        $this->path = $path;

        // debug
        $this->debug = $debug;

        // extension config file
        $file = $path . '/extension.json';
        $this->config = $this->readExtensionConfigJson($file);
    }

    /**
     * get list extension from original json file
     *
     * @return \Illuminate\Support\Collection
     */
    public function listFromJson(): Collection
    {
        return New Collection($this->list);
    }

    /**
     * Load extension
     *
     * @return array
     */
    public function load(): array
    {
        if (count($this->config) == 0) return [];
        $installed = $this->getListInstalledExtension();
        $active = $this->getListActiveExtension($installed);

        $loaded = [];
        foreach ($installed as $item)
        {
            // make tmp for loaded
            $tmp = (object) [
                'name' => $item,
                'errors' => [],
                'loaded' => false,
                'registered' => false,
                'booted' => false,
                'active' => false
            ];

            // check extension has active list ?
            $check_active = array_search($item, $active);
            if ($check_active !== false)
            {
                $tmp->active = true;
            }

            // get config file
            $config = $this->getConfigExtension($item);
            $tmp->config = $config;
            $tmp->loaded = true;
            $loaded[] = $tmp;
        }
        $this->loaded = $loaded;

        // return
        return $loaded;
    }

    /**
     * Register a extension
     *
     * @param array $loaded
     * @return array
     */
    public function register(array $loaded = [])
    {
        $registered = [];
        foreach($loaded as $item)
        {
            // make tmp for loaded
            $tmp = (object) $item;

            // register if extension is active
            if ($tmp->active)
            {
                // set registered state
                $tmp->registered = true;

                // construct service provider
                if (!$this->debug) {
                    try {
                        // make wrapper for provider
                        $provider = new WrapperServiceProvider(
                            $this->path,
                            $item->name,
                            $tmp->config->provider
                        );
                        $tmp->provider = $provider;
                    } catch (\Throwable $th) {
                        $tmp->registered = false;
                        $tmp->errors[] = [
                            'title' => 'Error On Construct Service Provider',
                            'throwable' => $th
                        ];
                    }
                } else {
                    // make wrapper for provider
                    $provider = new WrapperServiceProvider(
                        $this->path,
                        $tmp->name,
                        $tmp->config->provider
                    );
                    $tmp->provider = $provider;
                }

                // register 
                if (!$this->debug) {
                    try {
                        // run register provider
                        $provider->register();
                    } catch (\Throwable $th) {
                        $tmp->registered = false;
                        $tmp->errors[] = [
                            'title' => 'Error On Register Service Provider',
                            'throwable' => $th
                        ];
                    }
                } else {
                    // run register provider
                    $provider->register();
                }
            } else {
                // set registered state
                $tmp->registered = false;
            }
            
            $registered[] = $tmp;
        }
        
        // return
        return $registered;
    }

    /**
     * Booting a extension provider
     *
     * @param array $loaded List of loaded extension
     * @return array lsit booted extension
     */
    public function boot(array $loaded = [])
    {
        $booted = [];
        foreach($loaded as $item)
        {
            // booting to extension if extension registered
            if (!isset($item->registered)) throw new ExtensionException(
                "Extension `{$item->name}` Not Have Registered Status"
            );
            if ($item->registered)
            {
                    //    
                    $item->booted = true;

                    // booting extension
                    $provider = $item->provider;
                    if (!$this->debug) {
                        try {
                            // run boot provider
                            $provider->boot();
                        } catch (\Throwable $th) {
                            $item->booted = false;
                            $item->errors[] = [
                                'title' => 'Error On boot Service Provider',
                                'throwable' => $th
                            ];
                        }
                    } else {
                        // run boot provider
                        $provider->boot();
                    }         

                } else {
                    $item->booted = false;
                }

                // list extension
                $booted[] = $item;
        }

        return $booted;
    }

    /**
     * Get List of Loaded Extension
     *
     * @return array
     */
    public function getLoaded()
    {
        return $this->loaded;
    }

    /**
     * Get List of Booted Extension
     *
     * @return array
     */
    public function getBooted()
    {
        return $this->booted;
    }

    /**
     * Read Extension Config Json
     *
     * @param string $file
     * @return void
     */
    private function readExtensionConfigJson(string $file): array
    {
        // check file is a file
        if (!File::isFile($file))
        {
            if (app()->runningInConsole()) return [];
            throw new NotFoundExtensionGlobalConfigException(
                'Extension Config Json Not Found `' . $file . '`'
            );
        }

        // check file writeable
        if (!File::isWritable($file))
        {
            throw new ExtensionException(
                'Extension Config Json Not Writeable `' . $file . '`'
            );
        }

        // get from json file
        $config = json_decode(file_get_contents($file), true);

        // validate
        if (!isset($config['list'])) throw new ExtensionException(
            'Extension Config Json Invalid Configuration: `list` not found (' . $file . ')'
        ); 
        if (!isset($config['active'])) throw new ExtensionException(
            'Extension Config Json Invalid Configuration: `active` not found (' . $file . ')'
        ); 
        
        // return
        return $config;
    }

    /**
     * Get list installed from extension json
     *
     * @param array $installed
     * @return array
     */
    private function getListInstalledExtension(): array
    {
        $lists = $this->config['list'];

        // validate installed extension
        foreach ($lists as $index => $item)
        {
            $continue = true;
            $path = $this->path . '/' . $item;
            $config_path = $path . '/extension.json';
            
            // check folder
            if (!File::isDirectory($path))
            {
                if ($this->debug) throw new NotFoundExtensionException(
                    'Extension `' . $item . '` Doesnt have working folder (' . $path . ')'
                );
                $continue = false;
            }

            // check extension config
            if (!File::isFile($config_path))
            {
                if ($this->debug) throw new NotFoundExtensionConfigException(
                    'Extension `' . $item . '` Doesnt have config file (' . $config_path . ')'
                );
                $continue = false;
            }

            // continue
            if (!$continue) array_splice($lists, $index, 1);
        }

        return $lists;
    }

    /**
     * Get list active from extension json
     *
     * @param array $installed
     * @return array
     */
    private function getListActiveExtension(array $installed = [])
    {
        $lists = $this->config['active'];

        // validate list active must be installed
        foreach ($lists as $index => $item)
        {
            if (!in_array($item, $installed)) array_splice($lists, $index, 1);
        }

        return $lists;
    }

    /**
     * Get Config from extension
     *
     * @param string $extension
     * @return object
     */
    private function getConfigExtension(string $extension)
    {
        $path = $this->path . '/' . $extension;
        $config_path = $path . '/extension.json';
        
        // check extension config
        if (!File::isFile($config_path))
        {
            if ($this->debug) throw new NotFoundExtensionException(
                'Extension `' . $extension . '` Doesnt have config file (' . $config_path . ')'
            );
        }

        // load
        $config = (object) json_decode(file_get_contents($config_path));
        
        // validate
        $config_must_exist = ['name', 'description', 'provider', 'version', 'author'];
        foreach($config_must_exist as $item)
        {
            if (!isset($config->$item)) throw new NotFoundExtensionException(
                "Extension `{$extension}` doesnt have `{$item}` in config. ({$config_path})"
            );
        }
        

        // return
        return $config;
    }


    /**
     * Update list installed extension
     *
     * @return \Illuminate\Support\Collection
     */
    public function updateList() : Collection
    {
        // vars
        $file = $this->path . '/extension.json';
        
        // get current config
        $config = $this->readExtensionConfigJson($file);

        // scan dir in extension folder
        $files = scandir($this->path); array_splice($files, 0, 2);
        $result = [];
        foreach($files as $item)
        { if (File::isDirectory("{$this->path}/{$item}")) $result[] = $item; }

        // write new extension detected
        $config["list"] = $result;
        $this->writeExtensionConfigJson($file, $config);

        // return
        return new Collection($config);
    }

    private function writeExtensionConfigJson(string $file, array $config)
    {
        $config = (object) json_decode(json_encode($config));
        $config_json = json_encode($config, JSON_PRETTY_PRINT);
        $write = file_put_contents($file, $config_json);
        return $write;
    }

    /**
     * Enable a extension
     *
     * @param string $name
     * @return bool true if success
     */
    public function enable(string $name)
    {
        $file = $this->path . '/extension.json';
        $config = $this->config;

        // search array in list installed
        $search_list = array_search($name, $config["list"]);
        if ($search_list === false)
        {
            if ($this->debug || app()->runningInConsole()) throw new ExtensionException(
                "Extension `{$name}` not installed."
            );
            return false;
        }

        // search if exist or not, and then add and save
        $search_active = array_search($name, $config["active"]);
        if (!$search_active)
        {
            $config["active"][] = $name;
            $this->writeExtensionConfigJson($file, $config);
        }

        // if success
        return true;
    }

    /**
     * Disable a extension
     *
     * @param string $name
     * @return bool true if success
     */
    public function disable(string $name)
    {
        $file = $this->path . '/extension.json';
        $config = $this->config;

        // search if enabling or not, and then remove from list
        $search_active = array_search($name, $config["active"]);
        if ($search_active)
        {
            array_splice($config["active"], $search_active, 1);
            $this->writeExtensionConfigJson($file, $config);
        }

        // if success
        return true;
    }
}