<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestController extends AbstractController
{
    private HttpClientInterface $httpbinClient;

    public function __construct(HttpClientInterface $httpbinClient)
    {
        $this->httpbinClient = $httpbinClient;
    }

    /**
     * @Route("/request")
     */
    public function request(): Response
    {
        $response = $this->httpbinClient->request('GET', '/ip');
        $responseBody = $response->toArray();

        return $this->json([
            'data' => $responseBody,
        ]);
    }

    /**
     * @Route("/delay")
     */
    public function delay(): Response
    {
        $response = $this->httpbinClient->request('GET', '/delay/4');
        $responseBody = $response->toArray();

        return $this->json([
            'data' => $responseBody,
        ]);
    }
}
