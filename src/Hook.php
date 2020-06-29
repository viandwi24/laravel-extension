<?php

namespace Viandwi24\LaravelExtension;

use Illuminate\Support\Collection;
use Viandwi24\LaravelExtension\Exceptions\ExtensionException;

class Hook
{
    private array $actions = [];
    private array $filters = [];

    /**
     * Add Action
     *
     * @param string $extension extension name
     * @param string $name action name / action key
     * @param \Closure $callback action event
     * @return void
     */
    public function addAction(string $extension, string $name, \Closure $callback, int $priority = 10)
    {
        // valdiate actions
        if (!isset($this->actions[$name])) $this->actions[$name] = [];

        // add
        $this->actions[$name][] = (object) [
            'extension' => $extension,
            'name' => $name,
            'callback' => $callback,
            'priority' => $priority
        ];
    }

    /**
     * Get list of action
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAction(): Collection
    {
        $actions = new Collection($this->actions);
        return $actions;
    }

    /**
     * Add Filter
     *
     * @param string $extension extension name
     * @param string $name filter name / filter key
     * @param \Closure $callback filter event
     * @return void
     */
    public function addFilter(string $extension, string $name, \Closure $callback, int $priority = 10)
    {
        // valdiate filters
        if (!isset($this->filters[$name])) $this->filters[$name] = [];

        // add
        $this->filters[$name][] = (object) [
            'extension' => $extension,
            'name' => $name,
            'callback' => $callback,
            'priority' => $priority
        ];
    }

    /**
     * Get list of filter
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFilter(): Collection
    {
        $filters = new Collection($this->filters);
        return $filters;
    }

    /**
     * Run a action
     *
     * @param string $name action hook name
     * @return void
     */
    public function runAction(string $name)
    {
        // param
        $args = func_get_args();
        unset($args[0]);

        // search action by name
        $actions = new Collection($this->actions[$name]);
        $run_actions = $actions->sortBy('priority', SORT_NUMERIC, true);

        foreach($run_actions as $action)
        {
            call_user_func($action->callback, ...$args);
        }
    }

    /**
     * Remove action
     *
     * @param string $name
     * @param integer $priority
     * @return void
     */
    public function removeAction(string $name, int $priority = 100)
    {
        $actions = new Collection($this->actions[$name]);

        $result = $actions->filter(function ($value) use ($priority) {
            return $value->priority > $priority;
        });

        $this->actions[$name] = $result->toArray();
    }

    /**
     * Apply a filter
     *
     * @param string $name
     * @param mixed $value
     * @param mixed ...$params
     * @return void
     */
    public function applyFilter(string $name, $value, ...$params)
    {
        // param
        $args = func_get_args();
        unset($args[0]);
        unset($args[1]);

        // if filter not exist
        if (!isset($this->filters[$name])) return $value;

        // search filter by name
        $filters = new Collection($this->filters[$name]);

        // sort by priority
        $run_filters = $filters->sortBy('priority', SORT_NUMERIC, true);
        
        // run all filters
        $result = $value;
        foreach($run_filters as $filter)
        {
            $result = call_user_func($filter->callback, $result, ...$args);
        }

        return $result;
    }
}