<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Container\Container;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Dumper\PoFileDumper;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;

class ConfigTest extends TestCase
{
    private $container;
    private $signer;

    public function setUp(): void
    {
        // TODO : code à améliorer, cela permet de faire fonctionner les fonctions directory() et env() car elles utilisent l'instance globale du container !!!!
        $this->container = new Container();
    }

    public function testDefaultLocale(): void
    {
        $config = new TranslatorConfig([
            'locale' => 'ru'
        ]);

        $this->assertSame('ru', $config->getDefaultLocale());
    }

    public function testDefaultDomain(): void
    {
        $config = new TranslatorConfig([
            'locale' => 'ru'
        ]);

        $this->assertSame('messages', $config->getDefaultDomain());
    }

    public function testFallbackLocale(): void
    {
        $config = new TranslatorConfig([
            'fallback_locale' => 'ru'
        ]);

        $this->assertSame('ru', $config->getFallbackLocale());
    }

    public function testRegisterMessages(): void
    {
        $config = new TranslatorConfig(['auto_register' => true]);
        $this->assertTrue($config->isAutoRegisterMessages());

        $config = new TranslatorConfig(['auto_register' => false]);
        $this->assertFalse($config->isAutoRegisterMessages());
    }

    public function testLocalesDirectory(): void
    {
        $config = new TranslatorConfig([
            'directory' => 'directory/'
        ]);

        $this->assertSame('directory/', $config->getLocalesDirectory());
    }

    public function testLocaleDirectory(): void
    {
        $config = new TranslatorConfig([
            'directory' => 'directory/'
        ]);

        $this->assertSame('directory/ru/', $config->getLocaleDirectory('ru'));
    }

    public function testDomains(): void
    {
        $config = new TranslatorConfig([
            'domains' => [
                'spiral'   => [
                    'spiral-*'
                ],
                'messages' => ['*']
            ]
        ]);

        $this->assertSame('spiral', $config->resolveDomain('spiral-views'));
        $this->assertSame('messages', $config->resolveDomain('vendor-views'));
    }

    public function testDomainsFallback(): void
    {
        $config = new TranslatorConfig([
            'domains' => [
                'spiral' => [
                    'spiral-*'
                ]
            ]
        ]);

        $this->assertSame('external', $config->resolveDomain('external'));
    }

    public function testHasLoader(): void
    {
        $config = new TranslatorConfig([
            'loaders' => ['php' => PhpFileLoader::class]
        ]);

        $this->assertTrue($config->hasLoader('php'));
        $this->assertFalse($config->hasLoader('txt'));
    }

    public function testGetLoader(): void
    {
        $config = new TranslatorConfig([
            'loaders' => ['php' => PhpFileLoader::class]
        ]);

        $this->assertInstanceOf(LoaderInterface::class, $config->getLoader('php'));
    }

    public function testHasDumper(): void
    {
        $config = new TranslatorConfig([
            'dumpers' => ['po' => PoFileDumper::class]
        ]);

        $this->assertTrue($config->hasDumper('po'));
        $this->assertFalse($config->hasDumper('xml'));
    }

    public function testGetDumper(): void
    {
        $config = new TranslatorConfig([
            'dumpers' => ['po' => PoFileDumper::class]
        ]);

        $this->assertInstanceOf(DumperInterface::class, $config->getDumper('po'));
    }
}
