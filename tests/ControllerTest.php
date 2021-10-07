<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    public function testRedirectionToRouteAdmin(): void
    {

        $client = static::createClient();
        $client->request('GET','/admin');


        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
