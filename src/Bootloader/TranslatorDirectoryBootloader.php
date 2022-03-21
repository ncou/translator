<?php

declare(strict_types=1);

namespace Chiron\Translator\Bootloader;

use Chiron\Core\Container\Bootloader\AbstractBootloader;
use Chiron\Core\Directories;

final class TranslatorDirectoryBootloader extends AbstractBootloader
{
    public function boot(Directories $directories): void
    {
        if (! $directories->has('@locale')) {
            $directories->set('@locale', $directories->get('@resources/locale/'));
        }
    }
}
