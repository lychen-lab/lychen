<?php

namespace App\Controller;

use App\Service\PlantVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('api_doc');
    }

    #[Route('/plant-verifier/{plantUlid}', name: 'plant-verifier')]
    public function test(PlantVerifier $plantVerifier, string $plantUlid): Response
    {
        $plantVerifier->assertPlantExists($plantUlid);

        return new Response('cv');
    }
}
