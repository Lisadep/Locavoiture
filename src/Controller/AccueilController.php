<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    //Redirection du localhost vers accueil ac la fonction Symfony redirectToRoute()
    // comme en PHP header:Location
    #[Route('/')]
    public function locaVoiture()
    {
        return $this->redirectToRoute('accueil');
    }
    
    #[Route('/accueil', name: 'pageAccueil')]
    public function accueilListeDesVoitures(VoitureRepository $repository): Response
    {
        // Logique de programmation :
        // Aller chercher la liste des voitures de la BDD qui sont 'active'
        $voitures = $repository->findby(["active" => 1]);
        //dd($voitures);

        // Afficher en front le prix /100 pr av un prix correct
        // $prix = 'prix';
        // Passage en revue de chaque prix de voitures pr diviser son prix par 100
        // sans enregistrer en BDD

        foreach ($voitures as $key => $voiture):
            $prixOrigine = $voiture->getPrix();
            // transformation du prix
            $prixTransforme = floatval($prixOrigine/100);
            $voiture->setPrix($prixTransforme);
        endforeach;

        // retour de la vue
        return $this->render('accueil/accueil.html.twig', [
            'pageAccueil' => 'AccueilController',
            'voitures' => $voitures,
        ]);
    }
}