<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterFormTest extends WebTestCase
{
    public function testRegisterForm()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $buttonCrawlerNode = $crawler->selectButton('submit');

        $form = $buttonCrawlerNode->form();

        $form = $buttonCrawlerNode->form([
            'register[FirstName]'    => 'Azerty',
            'register[LastName]' => 'Uiopqsd',
            'register[Email]'    => 'Azerty@Azerty.fr',
            'register[Birthdate][year]' => 1984,
            'register[Birthdate][month]'=> 2,
            'register[Birthdate][day]'=> 1,
            'register[Password]' => 'Azerty',

        ]);

        $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
    }
}