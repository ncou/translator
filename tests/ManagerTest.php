<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Chiron\Translator\Catalogue\CacheInterface;
use Chiron\Translator\Catalogue\CatalogueLoader;
use Chiron\Translator\Catalogue\CatalogueManager;
use Chiron\Translator\CatalogueInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Translator\Exception\LocaleException;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\PoFileLoader;

class ManagerTest extends TestCase
{
    public function testLocalesFromLoader(): void
    {
        $cache = m::mock(CacheInterface::class);
        $cache->shouldReceive('getLocales')->andReturn(null);
        $cache->shouldReceive('setLocales')->andReturn(null);

        $manager = new CatalogueManager(new CatalogueLoader(new TranslatorConfig([
                'directory' => __DIR__ . '/fixtures/locales/',
                'loaders'   => [
                    'php' => PhpFileLoader::class,
                    'po'  => PoFileLoader::class,
                ]
            ])), $cache);

        $this->assertTrue($manager->has('ru'));
        $this->assertTrue($manager->has('en'));
    }

    public function testLocalesFromMemory(): void
    {
        $cache = m::mock(CacheInterface::class);
        $cache->shouldReceive('getLocales')->andReturn(['en', 'ru']);
        $cache->shouldNotReceive('setLocales')->andReturn(null);

        $manager = new CatalogueManager(new CatalogueLoader(new TranslatorConfig([
                'directory' => __DIR__ . '/fixtures/locales/',
                'loaders'   => [
                    'php' => PhpFileLoader::class,
                    'po'  => PoFileLoader::class,
                ]
            ])), $cache);

        $this->assertTrue($manager->has('ru'));
        $this->assertTrue($manager->has('en'));
    }

    public function testCatalogue(): void
    {
        $cache = m::mock(CacheInterface::class);
        $cache->shouldReceive('getLocales')->andReturn(['en', 'ru']);

        $manager = new CatalogueManager(new CatalogueLoader(new TranslatorConfig([
                'directory' => __DIR__ . '/fixtures/locales/',
                'loaders'   => [
                    'php' => PhpFileLoader::class,
                    'po'  => PoFileLoader::class,
                ]
            ])), $cache);

        $cache->shouldReceive('loadLocale')->with('ru')->andReturn([]);

        $catalogue = $manager->get('ru');
        $this->assertInstanceOf(CatalogueInterface::class, $catalogue);

        $this->assertTrue($catalogue->has('messages', 'message'));
        $this->assertSame('translation', $catalogue->get('messages', 'message'));

        $cache->shouldReceive('saveLocale')->with(
            'ru',
            [
                'messages' => [
                    'message' => 'translation'
                ],
                'views'    => [
                    'Welcome To Spiral' => '?????????? ???????????????????? ?? Spiral Framework',
                    'Twig Version'      => 'Twig ????????????'
                ]
            ]
        )->andReturn(null);

        $cache->shouldReceive('saveLocale')->with(
            'ru',
            [
                'messages' => [
                    'message' => 'new message'
                ],
                'views'    => [
                    'Welcome To Spiral' => '?????????? ???????????????????? ?? Spiral Framework',
                    'Twig Version'      => 'Twig ????????????'
                ]
            ]
        )->andReturn(null);

        $catalogue->set('messages', 'message', 'new message');
        $manager->save('ru');
    }

    public function testFromMemory(): void
    {
        $cache = m::mock(CacheInterface::class);
        $cache->shouldReceive('getLocales')->andReturn(['en', 'ru']);

        $cache->shouldReceive('loadLocale')->with(
            'ru'
        )->andReturn([
            'messages' => [
                'message' => 'new message'
            ],
            'views'    => [
                'Welcome To Spiral' => '?????????? ???????????????????? ?? Spiral Framework',
                'Twig Version'      => 'Twig ????????????'
            ]
        ]);

        $manager = new CatalogueManager(new CatalogueLoader(new TranslatorConfig([
                'directory' => __DIR__ . '/fixtures/locales/',
                'loaders'   => [
                    'php' => PhpFileLoader::class,
                    'po'  => PoFileLoader::class,
                ]
            ])), $cache);

        $cache->shouldReceive('loadLocale')->with('ru')->andReturn([]);

        $catalogue = $manager->get('ru');
        $this->assertInstanceOf(CatalogueInterface::class, $catalogue);

        $this->assertTrue($catalogue->has('messages', 'message'));
        $this->assertSame('new message', $catalogue->get('messages', 'message'));

        $cache->shouldReceive('setLocales')->with(null);
        $cache->shouldReceive('saveLocale')->with('ru', null);
        $cache->shouldReceive('saveLocale')->with('en', null);

        $manager->reset();
    }
}
