<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MightFailController extends AbstractController
{
    private HttpClientInterface $httpbinClient;

    public function __construct(HttpClientInterface $httpbinClient)
    {
        $this->httpbinClient = $httpbinClient;
    }

    /**
     * @Route("/mightFail")
     */
    public function mightFail(): Response
    {
        $response = $this->httpbinClient->request('GET', '/status/200,404,500');
        $status = $response->getStatusCode();

        if ($status < 300) {
            return $this->json(['success' => true]);
        }

        if ($status === 404) {
            return $this->json(['success' => false], 404);
        }

        $response->getHeaders();

        return $this->json([]);
    }
}
