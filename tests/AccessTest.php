<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessTest extends WebTestCase
{
    const ROUTES = [
        ['GET', '/'],
        ['GET', '/login'],
        ['GET', '/register'],
    ];

    public function testSomething(): void
    {
        $client = static::createClient();
        foreach (self::ROUTES as list($type,$route)) {
            $crawler = $client->request($type, $route);
            $this->assertResponseIsSuccessful();
        }
    }
}
