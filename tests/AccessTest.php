<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessTest extends WebTestCase
{
    const ROUTES = [
        ['GET', '/'],
        ['GET', '/login'],
        ['GET', '/register'],
        ['GET', '/logout'],
        ['GET', '/profile'],
        ['GET', '/timesheet'],
        ['GET', '/timesheet/new'],
        ['GET', '/timesheet/1'],
        ['GET', '/timesheet/1/edit'],
        ['GET', '/timesheet/1/delete'],
        ['GET', '/user'],
        ['GET', '/user/new'],
        ['GET', '/user/1'],
        ['GET', '/user/1/edit'],
        ['GET', '/user/1/delete'],
    ];





    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
