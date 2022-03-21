<?php

declare(strict_types=1);

namespace Chiron\Translator\Catalogue;

use Chiron\Translator\Catalogue;
use Chiron\Translator\CatalogueInterface;
use Chiron\Translator\Config\TranslatorConfig;
use Chiron\Filesystem\Filesystem;
use SplFileInfo;

final class CatalogueLoader implements LoaderInterface
{
    /** @var TranslatorConfig */
    private $config;

    public function __construct(TranslatorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function hasLocale(string $locale): bool
    {
        $locale = preg_replace('/[^a-zA-Z_]/', '', mb_strtolower($locale)); // Utilité de garder dans le regex A-Z alors qu'on a fait un strtolower ????

        return is_dir($this->config->getLocaleDirectory($locale));
    }

    /**
     * @inheritdoc
     */
    public function getLocales(): array
    {
        if (!is_dir($this->config->getLocalesDirectory())) {
            return [];
        }

        $filesystem = new Filesystem(); // TODO : déplacer cette instanciation dans le constructeur et mettre en variable de classe le $this->filesystem ???
        $directories = $filesystem->directories($this->config->getLocalesDirectory());

        $locales = [];
        foreach ($directories as $directory) {
            $locales[] = $directory->getFilename();
        }

        return $locales;
    }

    /**
     * @inheritdoc
     */
    public function loadCatalogue(string $locale): CatalogueInterface
    {
        $locale = preg_replace('/[^a-zA-Z_]/', '', mb_strtolower($locale));
        $catalogue = new Catalogue($locale);

        if (!$this->hasLocale($locale)) {
            return $catalogue;
        }

        $filesystem = new Filesystem();
        $files = $filesystem->files($this->config->getLocaleDirectory($locale));

        foreach ($files as $file) {
            /*
            $this->getLogger()->info(
                sprintf(
                    "found locale domain file '%s'",
                    $file->getFilename()
                ),
                ['file' => $file->getFilename()]
            );*/

            //Per application agreement domain name must present in filename
            $domain = strstr($file->getFilename(), '.', true);

            if (!$this->config->hasLoader($file->getExtension())) {
                /*
                $this->getLogger()->warning(
                    sprintf(
                        "unable to load domain file '%s', no loader found",
                        $file->getFilename()
                    ),
                    ['file' => $file->getFilename()]
                );*/

                continue;
            }

            $catalogue->mergeFrom(
                $this->config->getLoader($file->getExtension())->load(
                    (string)$file,
                    $locale,
                    $domain
                )
            );
        }

        return $catalogue;
    }
}
