<?php

declare(strict_types=1);

namespace Chiron\Translator\Catalogue;

final class NullCache implements CacheInterface
{
    /**
     * @inheritdoc
     */
    public function setLocales(?array $locales): void
    {
    }

    /**
     * @inheritdoc
     */
    public function getLocales(): ?array
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function saveLocale(string $locale, ?array $data): void
    {
    }

    /**
     * @inheritdoc
     */
    public function loadLocale(string $locale): ?array
    {
        return null;
    }
}
