<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginFormTest extends WebTestCase
{
    public function testLoginForm()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $buttonCrawlerNode = $crawler->selectButton('submit');

        $form = $buttonCrawlerNode->form();

        // dd($form);

        $form = $buttonCrawlerNode->form([
            'email'    => 'admin@cinesuper.com',
            'password' => '12345678',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
    }
}