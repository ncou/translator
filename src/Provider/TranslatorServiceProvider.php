<?php

declare(strict_types=1);

namespace Chiron\Translator\Provider;

use Chiron\Container\BindingInterface;
use Chiron\Container\Container;
use Chiron\Core\Container\Provider\ServiceProviderInterface;
use Chiron\Core\Exception\ScopeException;
use Chiron\Http\Config\HttpConfig;
use Chiron\Routing\MatchingResult;
use Chiron\Routing\Route;
use Chiron\Routing\Map;
use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Translation\IdentityTranslator;
use Chiron\Translator\Translator;
use Chiron\Translator\TranslatorInterface;
use Chiron\Translator\CatalogueManagerInterface;
use Chiron\Translator\Catalogue\CatalogueManager;
use Chiron\Translator\Catalogue\LoaderInterface;
use Chiron\Translator\Catalogue\CatalogueLoader;
use Chiron\Translator\Catalogue\CacheInterface;
use Chiron\Translator\Catalogue\MemoryCache;

/**
 * Chiron Translator services provider.
 */
class TranslatorServiceProvider implements ServiceProviderInterface
{
    /**
     * Register Chiron routing services.
     *
     * @param BindingInterface $binder
     */
    public function register(BindingInterface $binder): void
    {
        // TODO : utilité de ce binding avec l'interface de Symfony ????
        $binder->singleton(\Symfony\Contracts\Translation\TranslatorInterface::class, TranslatorInterface::class);
        $binder->singleton(TranslatorInterface::class, Translator::class);
        $binder->singleton(CatalogueManagerInterface::class, CatalogueManager::class);
        $binder->singleton(LoaderInterface::class, CatalogueLoader::class);
        $binder->singleton(CacheInterface::class, MemoryCache::class);

        $binder->singleton(IdentityTranslator::class, Closure::fromCallable([$this, 'identityTranslator']));
    }

    /**
     * @return IdentityTranslator
     */
    private function identityTranslator(): IdentityTranslator
    {
        return new IdentityTranslator();
    }
}
