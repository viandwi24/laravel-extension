<?php

namespace Viandwi24\LaravelExtension\Exceptions;

use Exception;
use Facade\IgnitionContracts\Solution;
use Facade\IgnitionContracts\ProvidesSolution;
use Viandwi24\LaravelExtension\Solutions\NotFoundExtensionProviderSolution;

class NotFoundExtensionProviderException extends Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        return new NotFoundExtensionProviderSolution;
    }
}