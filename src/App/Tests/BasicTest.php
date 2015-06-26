<?php

namespace App\Tests;

use Silex\WebTestCase;

class BasicTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../../app.php';
        $app['debug'] = true;         return $app;
    }

    public function testStatusRoute()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(
            'Executando!',
            $client->getResponse()->getContent()
            );
    }

    public function testProcessData()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/processar-dados');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("Tempo")'));
        $this->assertCount(4, $crawler->filter('h3:contains("Manaus")'));
    }

}