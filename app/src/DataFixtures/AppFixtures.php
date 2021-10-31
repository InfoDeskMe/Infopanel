<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {    
        $user = new Admin();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['role' => 'ROLE_ADMIN']);
        $user->setPassword('$2a$12$kjB7y7y1qxdjoP6kXnSww.vsrApRFm5n.ehXtwiLIcAUZNI5Dv9Xe');
        $manager->persist($user);
        $manager->flush();
    }
}