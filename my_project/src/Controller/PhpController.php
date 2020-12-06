<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhpController extends AbstractController
{
    /**
     * @Route("/php")
     */
    public function php(): Response
    {
        return $this->json([
            'Hello' => 'World!',
        ]);
    }
}
