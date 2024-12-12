<?php

namespace App\Prolyfix\RssBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class RssBundle extends AbstractBundle
{
    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'RssBundle';
    }
}