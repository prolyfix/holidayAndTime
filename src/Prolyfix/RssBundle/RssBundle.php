<?php

namespace App\Prolyfix\RssBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class RssBundle extends AbstractBundle
{
    public static function getShortName(): string
    {
        return 'RssBundle';
    }
}