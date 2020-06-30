<?php

namespace Viandwi24\LaravelExtension\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\Artisan;

class NotFoundExtensionGlobalConfigSolution implements RunnableSolution
{
    public function getSolutionTitle(): string
    {
        return 'Extension Global Config Not Found.';
    }

    public function getSolutionDescription(): string
    {
        return 'You must create global config for extension in your extension folder.`';
    }

    public function getDocumentationLinks(): array
    {
        return ['Laravel Extension Docs' => 'https://www.github.com/viandwi24/laravel-extension'];
    }

    public function getSolutionActionDescription(): string
    {
        return 'press the button below for simple init extension folder and init.';
    }

    public function getRunButtonText(): string
    {
        return 'Run Extension Init';
    }

    public function getRunParameters(): array
    {
        return [];
    }

    public function run(array $parameters = [])
    {
        Artisan::call('extension:init');
    }
}