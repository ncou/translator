<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Chiron\Core\BootloadManager;
use Chiron\Container\Container;
use Chiron\Core\MemoryInterface;
use Chiron\Core\NullMemory;
use Chiron\Translator\Catalogue;
use Chiron\Translator\Catalogue\LoaderInterface;
use Chiron\Translator\Catalogue\RuntimeLoader;
use Chiron\Translator\CatalogueManagerInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Translator\Translator;
use Chiron\Translator\TranslatorInterface;

class InterpolateTest extends TestCase
{
    public function testInterpolate(): void
    {
        $this->assertSame(
            'Welcome, Antony!',
            $this->translator()->trans('Welcome, Antony!', ['name' => 'Antony'])
        );
    }

    public function testInterpolateNumbers(): void
    {
        $this->assertSame(
            'Bye, Antony!',
            $this->translator()->trans('Bye, Antony!', ['Antony'])
        );
    }

    public function testInterpolateBad(): void
    {
        $this->assertSame(
            'Bye, {1}!',
            $this->translator()->trans('Bye, {1}!', [new self()])
        );
    }

    protected function translator(): Translator
    {
        $container = new Container();
        $container->bind(TranslatorConfig::class, new TranslatorConfig([
            'locale'  => 'en',
            'domains' => [
                'messages' => ['*']
            ]
        ]));

        $container->singleton(TranslatorInterface::class, Translator::class);
        $container->singleton(CatalogueManagerInterface::class, Catalogue\CatalogueManager::class);
        $container->bind(LoaderInterface::class, Catalogue\CatalogueLoader::class);

        $loader = new RuntimeLoader();
        $loader->addCatalogue('en', new Catalogue('en', [
            'messages' => [
                'Welcome, {name}!' => 'Welcome, {name}!',
                'Bye, {1}!'        => 'Bye, {1}!'
            ]
        ]));

        $container->bind(LoaderInterface::class, $loader);

        return $container->get(TranslatorInterface::class);
    }
}
