<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Viandwi24\LaravelExtension\Menu;

final class MenuBuilderTest extends TestCase
{
    public function testAssertEquals(): void
    {
        $menu = new Menu;
        
        // prepare
        $menu->add('navbar', "Home", "https://example.com");
        $menu->add('navbar', "About", "https://example.com/about");

        // render
        $html = $menu->render('navbar', function ($text, $url) {
            return "<li>{$text}-{$url}</li>";
        });

        // equal
        $expected = "<li>Home-https://example.com</li><li>About-https://example.com/about</li>";
        $this->assertEquals($expected, $html);
    }
}

