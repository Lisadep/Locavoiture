<?php

namespace App\Controller\Admin;

use App\Entity\Voiture;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class VoitureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Voiture::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            BooleanField::new('active', 'Actif en front')
                ->setTextAlign('center'),
            ImageField::new('image', 'Photo')
            // Indiquer le chemin de travail
                ->setBasePath('images/upload')
            // Indiquer où va être enregistré l'image qui provient d'un PC
                ->setUploadDir('public/images/upload')
            // fichier accepter
                ->setFormType(FileUploadType::class)
            // Transformation physique du fichier provenant d'un PC en un fichier unique ds le dossier images/upload
                ->setUploadedFileNamePattern('[randomhash].[extension]')
            // Formatage du fichier image à l'écran ds le back-office
                ->setTemplatePath('admin/admin_imageVoiture.html.twig'),
            // Il faut créer le dossier admin ds le dossier vue templates
            // Créer la vue admin_imageVoiture.html.twig qui est vide au départ
            // Aller chercher dans le bundle easyadmin dossier :
            // vendor/easycorp/easyadmin-bundle/src/ressources/views/crud/field/image.html.twig
            TextField::new('nom', 'Nom')
                ->setTextAlign('center'),
            TextField::new('marque', 'Marque')
                ->setTextAlign('center'),
            TextareaField::new('description', 'Description')
                ->setTextAlign('center'),
            TextareaField::new('couleur', 'Couleur')
                ->setTextAlign('center'),
            MoneyField::new('prix', 'Prix TTC')
                ->setCurrency('EUR')
                ->setTextAlign('center'),
        ];
    }
    
}
