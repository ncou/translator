<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Chiron\Container\Container;
use Chiron\Translator\Catalogue\CatalogueLoader;
use Chiron\Translator\Catalogue\CatalogueManager;
use Chiron\Translator\Catalogue\LoaderInterface;
use Chiron\Translator\CatalogueManagerInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Translator\Exception\LocaleException;
use Chiron\Translator\Translator;
use Chiron\Translator\TranslatorInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\PoFileLoader;

class TranslatorTest extends TestCase
{
    public function testIsMessage(): void
    {
        $this->assertTrue(Translator::isMessage('[[hello]]'));
        $this->assertFalse(Translator::isMessage('hello'));
    }

    public function testLocale(): void
    {
        $translator = $this->translator();
        $this->assertSame('en', $translator->getLocale());

        $translator->setLocale('ru');
        $this->assertSame('ru', $translator->getLocale());
    }

    public function testLocaleException(): void
    {
        $this->expectException(LocaleException::class);

        $translator = $this->translator();
        $translator->setLocale('de');
    }

    public function testDomains(): void
    {
        $translator = $this->translator();

        $this->assertSame('spiral', $translator->getDomain('spiral-views'));
        $this->assertSame('messages', $translator->getDomain('vendor-views'));
    }

    public function testCatalogues(): void
    {
        $translator = $this->translator();
        $this->assertCount(2, $translator->getCatalogueManager()->getLocales());
    }

    public function testTrans(): void
    {
        $translator = $this->translator();
        $this->assertSame('message', $translator->trans('message'));

        $translator->setLocale('ru');
        $this->assertSame('translation', $translator->trans('message'));
    }

    protected function translator(): Translator
    {
        $container = new Container();
        $container->bind(TranslatorConfig::class, new TranslatorConfig([
            'locale'    => 'en',
            'directory' => __DIR__ . '/fixtures/locales/',
            'loaders'   => [
                'php' => PhpFileLoader::class,
                'po'  => PoFileLoader::class,
            ],
            'domains'   => [
                'spiral'   => [
                    'spiral-*',
                ],
                'messages' => ['*'],
            ],
        ]));

        $container->singleton(TranslatorInterface::class, Translator::class);
        $container->singleton(CatalogueManagerInterface::class, CatalogueManager::class);
        $container->bind(LoaderInterface::class, CatalogueLoader::class);

        return $container->get(TranslatorInterface::class);
    }
}
