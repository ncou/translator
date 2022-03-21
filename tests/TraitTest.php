<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Chiron\Container\Container;
use Chiron\Core\MemoryInterface;
use Chiron\Core\NullMemory;
use Chiron\Translator\Bootloader\TranslatorBootloader;
use Chiron\Translator\Catalogue\CatalogueLoader;
use Chiron\Translator\Catalogue\CatalogueManager;
use Chiron\Translator\Catalogue\LoaderInterface;
use Chiron\Translator\CatalogueManagerInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Translator\Traits\TranslatorTrait;
use Chiron\Translator\Translator;
use Chiron\Translator\TranslatorInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\PoFileLoader;

class TraitTest extends TestCase
{
    use TranslatorTrait;

    private $container;

    public function setUp(): void
    {
        $this->container = new Container();

        $this->container->bind(TranslatorConfig::class, new TranslatorConfig([
            'locale'    => 'en',
            'directory' => __DIR__ . '/fixtures/locales/',
            'loaders'   => [
                'php' => PhpFileLoader::class,
                'po'  => PoFileLoader::class,
            ],
            'domains'   => [
                'messages' => ['*']
            ]
        ]));

        $this->container->singleton(TranslatorInterface::class, Translator::class);
        $this->container->singleton(CatalogueManagerInterface::class, CatalogueManager::class);
        $this->container->bind(LoaderInterface::class, CatalogueLoader::class);
    }

    public function testTranslate(): void
    {
        $this->assertSame('message', $this->say('message'));

        $this->container->get(TranslatorInterface::class)->setLocale('ru');

        $this->assertSame('translation', $this->say('message'));
        $this->assertSame('translation', $this->say('[[message]]'));
    }
}
