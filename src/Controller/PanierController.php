<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/accueil/panier', name: 'panier')]
    // On passe en paramètre de notre méthode les classeRepository dt on va av besoin pr questionner la BDD,
    // la classe request de symfony pr récupérer certaines choses (dont l'url) et la classe EntityManagerInterface
    // pour enregistrer en BDD
    public function listeChoixPanier(PanierRepository $repo1, VoitureRepository $repo2, UserRepository $repo3, Request $req, EntityManagerInterface $em): Response
    {
        // Notre logique de fonctionnement du panier
        // 1 - Voir si un panier existe pour le user connecté
        // Récupération de la personne connectée avec cette ligne spécifique à symfony
        $user = $this->getUser();
        // Récupération du panier lié à ce user
        $panier = $repo1->findOneBy(['user' => $user], ['id' => 'DESC']);
        
        // Récupération de l'id du choix de la voiture 
        $idVoitureChoix = $req->get('id');
        // dd($idVoitureChoix);
        // Si le panier est vide et que l'on a cliqué sur ajouter au panier
        if(($panier == null) && ($idVoitureChoix !== null)):
            // récupération des informations de la voiture choisie par rapport à l'id
            $infoVoitureChoix = $repo2->findOneById($idVoitureChoix);
            //dd($infoVoitureChoix);
            // On créer un nouveau panier
            $panier = new Panier;
            $panier->addVoiture($infoVoitureChoix);
            // dd($panier);
            // Mettre à jour le panier aux infos de l'utilisateur connecté
            $panier->setUser($user);
            // Faire disparaître la voiture en Front car elle est ds un panier
            $infoVoitureChoix->setActive(false);
            // Ancienne méthode d'enregistrement en BDD (si non c'est avec add)
            $em->persist($panier);
            $em->flush();
        elseif(($panier !== null) && ($idVoitureChoix !== null)):
                // le panier existe et une voiture est selctionnée
                // récupération des informations de la voiture choisie par rapport à l'id
            $infoVoitureChoix = $repo2->findOneById($idVoitureChoix);
            $panier->addVoiture($infoVoitureChoix);
            // dd($panier);
            // Mettre à jour le panier aux infos de l'utilisateur connecté
            $panier->setUser($user);
            // Faire disparaître la voiture en Front car elle est ds un panier
            $infoVoitureChoix->setActive(false);

            // Calcul du prix total
            // Récupérer le panier complet du user
            $PanierListeChoixVoiture = $repo1->findOneBy(['user' => $user]);
            // Récupération de la liste des voitures du user
            $listeChoixVoiture = $panier->getVoiture($PanierListeChoixVoiture);
            // Passage en revue des prix des véhicules
            $tableauPrixTotal=[];
            foreach($listeChoixVoiture as $key => $voiture):
                // Récupération du prix de la voiture en cours ds la boucle
                $prixVoiture = $voiture->getPrix();
                // array_push(tableau vide ci-dessus, on ajoute le prix en cours de la voiture)
                array_push($tableauPrixTotal, $prixVoiture);
            endforeach;
            // On fait la somme de tous les éléments du tableau $tableauPrixTotal
            $totalPrix = array_sum($tableauPrixTotal);
            // Nous préparons la variable $totalPrix ds le champ total_prix de la BDD
            $panier->setPrixTotal($totalPrix / 100);

            // Ancienne méthode d'enregistrement en BDD (si non c'est avec add)
            $em->persist($panier);
            $em->flush();
        endif;
        //-------------GESTION PANIER VIDE ou TOTAL PRIX DU PANIER = 0.00---------------
        // Si le panier est vide, dc null rediriger l'utilisateur vers une page qui dit que le panier est vide
        // Récupération de la liste de voiture du user
        $PanierListeChoixVoiture = $repo1->findOneBy(['user' => $user]);
        $panier->getVoiture($PanierListeChoixVoiture);
        if($panier == null || $panier->getPrixTotal() == 0.00):
             return $this->redirectToRoute('panierVide');
        endif;

        // Retour rendu vers la vue templates/panier/panier.html.twig
        return $this->render('panier/panier.html.twig', [
            // 'user' => $user,
            'panier' => $panier,
            // 'request' => $req
        ]);  
    }

    #[Route('/accueil/panierVide', name: 'panierVide')]
    public function panierVide(): Response
    {
        return $this->render('panier/panierVide.html.twig', [
            
        ]);
    }
    #[Route('/accueil/supprimerVoiturePanier', name: 'supprimerVoiturePanier')]
    public function supprimerUneVoitureDuPanier(PanierRepository $repo1, VoitureRepository $repo2, EntityManagerInterface $em, Request $req)
    {
        // Récupération de la personne connectée avec cette ligne spécifique à symfony
        $user = $this->getUser();
        // Récupération du panier lié à ce user
        $panier = $repo1->findOneBy(['user' => $user], ['id' => 'DESC']);
        // Récupération de l'id voiture à supprimer
        $idVoitureChoixSupprimer = $req->get('id');
        // Récupération de toutes les infos de la voiture
        $infoVoitureChoixSupprimer = $repo2->findOneById($idVoitureChoixSupprimer);
        // Supression de la voiture sélectionnée
        $panier->removeVoiture($infoVoitureChoixSupprimer);
        // Faire réapparaître cette voiture en front
        $infoVoitureChoixSupprimer->setActive(true);

        // Recalcul du prix
        // Récupérer le panier complet du user
        $PanierListeChoixVoiture = $repo1->findOneBy(['user' => $user]);
        // Récupération de la liste des voitures du user
        $listeChoixVoiture = $panier->getVoiture($PanierListeChoixVoiture);
        // Passage en revue des prix des véhicules
        $tableauPrixTotal=[];
        foreach($listeChoixVoiture as $key => $voiture):
            // Récupération du prix de la voiture en cours ds la boucle
            $prixVoiture = $voiture->getPrix();
            // array_push(tableau vide ci-dessus, on ajoute le prix en cours de la voiture)
            array_push($tableauPrixTotal, $prixVoiture);
        endforeach;
        // On fait la somme de tous les éléments du tableau $tableauPrixTotal
        $totalPrix = array_sum($tableauPrixTotal);
        // Nous préparons la variable $totalPrix ds le champ total_prix de la BDD
        $panier->setPrixTotal($totalPrix);

        $em->persist($panier);
        $em->flush();
        // Si le sys voit la variable $totalPrix = 0 type float on redirige vers la page panierVide
        if($totalPrix == 0.00):
            return $this->redirectToRoute('panierVide');
        endif;
        return $this->redirectToRoute('panier');
    }
}
