<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'pageAccueil')]
    public function accueilListeDesVoitures(VoitureRepository $repository): Response
    {
        $voitures = $repository->findby(["active" => 1]);
        // dd($voitures);

        return $this->render('accueil/accueil.html.twig', [
            'pageAccueil' => 'AccueilController',
            'voitures' => $voitures,
        ]);
    }
}