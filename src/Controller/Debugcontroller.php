<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{
    #[Route('/test-session', name: 'test_session')]
    public function testSession(): Response
    {
        $session = $this->container->get('session');
        $session->set('test', 'value');
        $value = $session->get('test', 'default');

        return new Response("Session test: " . $value);
    }
}