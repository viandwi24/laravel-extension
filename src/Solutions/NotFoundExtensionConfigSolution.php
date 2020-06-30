<?php

namespace Viandwi24\LaravelExtension\Solutions;

use Facade\IgnitionContracts\RunnableSolution;

class NotFoundExtensionConfigSolution implements RunnableSolution
{
    public function getSolutionTitle(): string
    {
        return 'Extension Config Not Found';
    }

    public function getSolutionDescription(): string
    {
        return 'You must create "extension.json" in working directory extension. ';
    }

    public function getDocumentationLinks(): array
    {
        return ['Laravel Extension Docs' => 'https://www.github.com/viandwi24/laravel-extension'];
    }

    public function getSolutionActionDescription(): string
    {
        return 'press the button below for create example config.';
    }

    public function getRunButtonText(): string
    {
        return 'Fix this for me';
    }

    public function run(array $parameters = [])
    {
        return "";
    }

    public function getRunParameters(): array
    {
        return [];
    }
}