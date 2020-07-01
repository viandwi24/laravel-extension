<?php declare(strict_types=1);

use Viandwi24\LaravelExtension\Hook;
use PHPUnit\Framework\TestCase;

final class HookActionTest extends TestCase
{
    public function testAssertEquals(): void
    {
        $hook = new Hook;
        $title = "";

        // 
        $hook->addAction('extension_1', 'getting_hello', function () use (&$title) {
            $title = "Hello World";
        }, 15);
        $hook->addAction('extension_2', 'getting_hello', function () use (&$title) {
            $title = "Hy, {$title}!";
        }, 10);

        // 
        $hook->runAction('getting_hello');
        $this->assertEquals(
            "Hy, Hello World!",
            $title
        );
    }
}

