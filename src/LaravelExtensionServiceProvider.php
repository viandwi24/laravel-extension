<?php

namespace Viandwi24\LaravelExtension;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Viandwi24\LaravelExtension\Commands\ExtensionListCommand;
use Viandwi24\LaravelExtension\Facades\Extension;
use Viandwi24\LaravelExtension\Facades\Hook;

class LaravelExtensionServiceProvider extends ServiceProvider
{
    private $loaded = [];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 
        define('HOOK_SYSTEM_PRIORITY', 15);

        // config
        $this->publishConfig();
        $this->bindConfig();

        // bind
        $this->bindClass();

        // register command
        $this->makeCustomConsoleCommand();

        // register blade directive
        $this->makeCustomBladeDirective();

        // load and register a extension
        $this->loaded = Extension::load();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // boot a extensionsion
        $booted = Extension::boot($this->loaded);
    }


    /**
     * Bind Config to Application Container
     *
     * @return void
     */
    private function bindConfig()
    {
        $configPath = __DIR__ . '/../config/extension.php';
        $this->mergeConfigFrom($configPath, 'extension');

        $this->app->bind('extension.config', function () {
            return $this->app['config']->get('extension');
        });
    }

    /**
     * Binc Class to Application Container
     *
     * @return void
     */
    private function bindClass()
    {
        $this->app->bind('extension', function () {
            $config = $this->app['config']->get('extension');
            return new \Viandwi24\LaravelExtension\Extension($config['path'], $config['debug']);
        });
        $this->app->bind('hook', function () {
            return new \Viandwi24\LaravelExtension\Hook;
        });
    }

    /**
     * For Publish Config Laravel
     *
     * @return void
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/' => base_path('config'),
        ]);
    }


    /**
     * Register command
     *
     * @return void
     */
    private function makeCustomConsoleCommand()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ExtensionListCommand::class
            ]);
        }
    }

    /**
     * Register blade directive
     *
     * @return void
     */
    private function makeCustomBladeDirective()
    {
        Blade::directive('applyfilter', function ($expression) {
            $args = explode(', ', $expression);
            $result = '';
            if (count($args) == 1)
            {
                $name = $args[0]; array_splice($args, 0, 1);
                $args_str = (count($args) > 0) ? ", " . implode(', ', $args) : "";
                $result = "<?php \$name = {$name}; \$args_str = '$args_str'; ob_start(); ?>";
            } else {
                $name = $args[0];
                $value = $args[1]; array_splice($args, 0, 2);
                $args_str = (count($args) > 0) ? ", " . implode(', ', $args) : "";
                $result = "<?php echo \Viandwi24\LaravelExtension\Facades\Hook::applyFilter({$name}, {$value}{$args_str}); ?>";
            }
            return $result;
        });

        Blade::directive('endapplyfilter', function ($expression) {
            $result = "<?php \$value = ob_get_contents(); ob_end_clean(); ?>";
            $result .= "<?php echo \Viandwi24\LaravelExtension\Facades\Hook::applyFilter(\$name, \$value); ?>";
            return $result;
        });
    }
}
