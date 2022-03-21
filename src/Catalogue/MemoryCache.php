<?php

declare(strict_types=1);

namespace Chiron\Translator\Catalogue;

use Chiron\Core\Memory;
use Chiron\Translator\Catalogue\CacheInterface;

final class MemoryCache implements CacheInterface
{
    /** @var Memory */
    private $memory;

    /**
     * @param Memory $memory
     */
    public function __construct(Memory $memory)
    {
        $this->memory = $memory;
    }

    /**
     * @inheritDoc
     */
    public function setLocales(?array $locales): void
    {
        $this->memory->write('i18n.locales', $locales);
    }

    /**
     * @inheritDoc
     */
    public function getLocales(): ?array
    {
        // TODO : il faudra vérifier si la section mémoire exist avant de la lire, sinon retourner null. Car on risque d'avoir le composant Memory qui throw une exception si la section n'est pas trouvée.
        return $this->memory->read('i18n.locales') ?? null;
    }

    /**
     * @inheritDoc
     */
    public function saveLocale(string $locale, ?array $data): void
    {
        $this->memory->write("i18n.{$locale}", $data);
    }

    /**
     * @inheritDoc
     */
    public function loadLocale(string $locale): ?array
    {
        return $this->memory->read("i18n.{$locale}") ?? null;
    }
}
