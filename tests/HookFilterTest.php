<?php declare(strict_types=1);

use Viandwi24\LaravelExtension\Hook;
use PHPUnit\Framework\TestCase;

final class HookFilterTest extends TestCase
{
    public function testAssertEquals(): void
    {
        $hook = new Hook;

        // 
        $hook->addFilter('extension_1', 'getting_hello', function ($text) {
            return "Hello {$text}";
        }, 15);
        $hook->addFilter('extension_2', 'getting_hello', function ($text) {
            return "Hy, {$text}!";
        }, 10);

        // 
        $result = $hook->applyFilter('getting_hello', "World");
        $this->assertEquals(
            "Hy, Hello World!",
            $result
        );
    }
}

