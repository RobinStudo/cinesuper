<?php 

namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GiftsPageControllerTest extends WebTestCase
{
    private $client = null;

    /**
     * SetUp Authenticate
     *
     */
    public function setUp()
    {
        $this->client = static::createClient([], [
        'PHP_AUTH_USER' => 'admin@cinesuper.com',
        'PHP_AUTH_PW'   => '12345678',
        ]);
    }

    /**
     * Simulate login and stock session
     *
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewallName, ['ROLE_ADMIN']);
         $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * Test secured page Gift
     *
     */
    public function testSecuredPageGift()
    {
        $this->logIn();
        $this->client->request('GET', '/gift');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test secured page dashboard
     *
     */
    public function testSecuredPageDaschboard()
    {
        $this->logIn();
        $this->client->request('GET', '/dashboard');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test secured page Admin
     *
     */
    public function testSecuredPageadmin()
    {
        $this->logIn();
        $this->client->request('GET', '/admin');
        $this->assertSame(301, $this->client->getResponse()->getStatusCode());
    }
    
}