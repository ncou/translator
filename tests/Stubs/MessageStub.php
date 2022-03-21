<?php

declare(strict_types=1);

namespace Chiron\Tests\Translator\Stubs;

use Chiron\Translator\Traits\TranslatorTrait;

class MessageStub
{
    use TranslatorTrait;

    private $messages = [
        '[[some-text]]'
    ];
}
