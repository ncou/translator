<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator;

use PHPUnit\Framework\TestCase;
use Chiron\Container\Container;
use Chiron\Translator\Catalogue;
use Chiron\Translator\Catalogue\LoaderInterface;
use Chiron\Translator\Catalogue\RuntimeLoader;
use Chiron\Translator\CatalogueManagerInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Translator\Translator;
use Chiron\Translator\TranslatorInterface;

class AutoRegisterTest extends TestCase
{
    public function testRegister(): void
    {
        $tr = $this->translator();

        $this->assertTrue($tr->getCatalogueManager()->get('en')->has('messages', 'Welcome, {name}!'));
        $this->assertFalse($tr->getCatalogueManager()->get('en')->has('messages', 'new'));

        $tr->trans('new');
        $this->assertTrue($tr->getCatalogueManager()->get('en')->has('messages', 'new'));
    }

    protected function translator(): Translator
    {
        $container = new Container();
        $container->bind(TranslatorConfig::class, new TranslatorConfig([
            'locale'       => 'en',
            'auto_register' => true,
            'domains'      => [
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
