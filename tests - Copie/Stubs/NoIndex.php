<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator\Stubs;

use Chiron\Translator\Traits\TranslatorTrait;

class NoIndex
{
    use TranslatorTrait;

    /**
     * @do-not-index
     */
    protected $mess = [
        '[[no-message]]'
    ];
}
