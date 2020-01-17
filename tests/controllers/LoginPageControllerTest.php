<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test Login Page 
 * 
 */
class LoginPageControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
    $client = static::createClient();
    $crawler = $client->request('GET', '/login');
    $form = $crawler->selectButton('Connexion')->form([
        'email' => 'admin@cinesuper.com',
        'password' => '123456'
    ]);
    $client->submit($form);
    $this->assertResponseRedirects('/login');
    $client->followRedirect();
    $this->assertSelectorExists('.alert.alert-danger');
    }
}