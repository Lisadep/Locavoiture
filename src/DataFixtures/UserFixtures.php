<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        $user = new User;
        $user->setEmail("depierrepont.lisa@gmail.com");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword('$2y$13$.MAD3wKF5a35cRuIwqFE6.OzRRtVoJtybIUmmCv3S3zpjbya52PKm');
        
        $manager->persist($user);

        $manager->flush();
    }
}