<?php

namespace Viandwi24\LaravelExtension\Extension;

use Viandwi24\LaravelExtension\Exceptions\NotFoundExtensionProviderException;

class WrapperServiceProvider
{
    public string $extension;
    public string $path;
    public string $provider_file;
    public string $provider_file_path;
    public $service_provider;
    public $provider;

    /**
     * Construct
     *
     */
    public function __construct(string $path, string $extension, string $provider_file)
    {
        $this->extension = $extension;
        $this->path = $path;
        $this->provider_file = $provider_file;
        $this->provider_file_path = $this->path . '/' . $extension . '/' . $provider_file;
        $this->provider = $this->construct();
        $this->service_provider = app()->make($this->provider);
    }

    /**
     * Construct extension service provicder
     *
     * @return string name in container
     */
    private function construct()
    {
        $name = 'extension.' . $this->extension;
        $provider_file = $this->provider_file_path . '.php';
        $provider_class = $this->provider_file;
        $provider_namespace = "\\Extension\\{$this->extension}\\{$provider_class}";

        // check service provider is class
        if (!class_exists($provider_namespace)) {
            throw new NotFoundExtensionProviderException(
                'Extension `' . $this->extension . 
                '` Doesnt have provider  class (' . $provider_namespace . ')'
            );
        }

        app()->singleton($name, function () use ($provider_namespace) {
            return new $provider_namespace(app());
        });
        return $name;
    }

    /**
     * run register method in extension service provider
     *
     * @return mixed
     */
    public function register()
    {
        return app()->make($this->provider)->register();
    }

    /**
     * run register method in extension service provider
     *
     * @return mixed
     */
    public function boot()
    {
        return app()->make($this->provider)->boot();
    }
}