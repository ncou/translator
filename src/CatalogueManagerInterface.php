<?php

declare(strict_types=1);

namespace Chiron\Translator;

use Chiron\Translator\Exception\LocaleException;

/**
 * Manages list of locales and associated catalogues.
 */
// TODO : déplacer cette classe dans le répertoire Catalogue ????
interface CatalogueManagerInterface
{
    /**
     * Get list of all existed locales.
     *
     * @return string[]
     */
    public function getLocales(): array;

    /**
     * Load catalogue.
     *
     * @throws LocaleException
     */
    public function load(string $locale): CatalogueInterface;

    /**
     * Save catalogue changes.
     */
    public function save(string $locale);

    /**
     * Check if locale exists.
     */
    public function has(string $locale): bool;

    /**
     * Get catalogue associated with the locale.
     *
     * @throws LocaleException
     */
    public function get(string $locale): CatalogueInterface;
}
