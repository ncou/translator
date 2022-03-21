<?php

declare(strict_types=1);

namespace Chiron\Translator\Config;

use Chiron\Config\AbstractInjectableConfig;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Closure;
use Chiron\Translator\Matcher;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Dumper;
use Symfony\Component\Translation\Loader;

final class TranslatorConfig extends AbstractInjectableConfig
{
    protected const CONFIG_SECTION_NAME = 'translator';

    /** @var Matcher */
    private $matcher;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->matcher = new Matcher();
    }

    protected function getConfigSchema(): Schema
    {
        return Expect::structure([
            'locale' => Expect::string()->default(env('LOCALE', 'en')),
            'fallback_locale' => Expect::string()->default(env('LOCALE', 'en')),
            'directory'      => Expect::string()->default(directory('locale')),
            'auto_register'   => Expect::boolean()->default(env('DEBUG', true)),
            'loaders'        => Expect::array()->default([
                'php'  => Loader\PhpFileLoader::class,
                'po'   => Loader\PoFileLoader::class,
                'csv'  => Loader\CsvFileLoader::class,
                'json' => Loader\JsonFileLoader::class,
            ]),
            'dumpers'        => Expect::array()->default([
                'php'  => Dumper\PhpFileDumper::class,
                'po'   => Dumper\PoFileDumper::class,
                'csv'  => Dumper\CsvFileDumper::class,
                'json' => Dumper\JsonFileDumper::class,
            ]),
            'domains'        => Expect::array()->default([
                // by default we can store all messages in one domain
                'messages' => ['*'],
            ]),
        ]);
    }

    /**
     * Default translation domain.
     */
    public function getDefaultDomain(): string
    {
        return 'messages';
    }

    public function getDefaultLocale(): string
    {
        return $this->get('locale');
    }

    public function getFallbackLocale(): string
    {
        return $this->get('fallback_locale') ?? $this->get('locale');
    }

    public function isAutoRegisterMessages(): bool
    {
        return !empty($this->get('auto_register'));
    }

    public function getLocalesDirectory(): string
    {
        return $this->get('directory');
    }

    public function getLocaleDirectory(string $locale): string
    {
        return $this->getLocalesDirectory() . $locale . '/';
    }

    /**
     * Get domain name associated with given bundle.
     */
    public function resolveDomain(string $bundle): string
    {
        $bundle = strtolower(str_replace(['/', '\\'], '-', $bundle));

        foreach ($this->get('domains') as $domain => $patterns) {
            foreach ($patterns as $pattern) {
                if ($this->matcher->matches($bundle, $pattern)) {
                    return $domain;
                }
            }
        }

        //We can use bundle itself as domain
        return $bundle;
    }

    public function hasLoader(string $extension): bool
    {
        return isset($this->get('loaders')[$extension]);
    }

    public function getLoader(string $extension): LoaderInterface
    {
        $class = $this->get('loaders')[$extension];

        return new $class();
    }

    public function hasDumper(string $dumper): bool
    {
        return isset($this->get('dumpers')[$dumper]);
    }

    public function getDumper(string $dumper): DumperInterface
    {
        $class = $this->get('dumpers')[$dumper];

        return new $class();
    }
}
