<?php
 
 namespace App\Tests;

 use Silex\WebTestCase;

 class BasicTest extends WebTestCase
 {
    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;         return $app;
    }

    public function testStatusRoute()
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(
            'Running with log',
            $client->getResponse()->getContent()
        );
    }

}