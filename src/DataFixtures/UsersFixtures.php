<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->addOrdinateurs($manager);
    }
    private function addOrdinateurs(ObjectManager $manager)
    {

        $users=[


                ['email' => 'toto1@toto.com', 'username' => 'toto1', 'password' => '1234'],
                ['email' => 'toto2@toto.com', 'username' => 'toto2', 'password' => '1234'],
                ['email' => 'toto3@toto.com', 'username' => 'yolesnoob', 'password' => '1234'],
        ];
        foreach ($users as $user)
        {
            $new_user = new User();
            $new_user->setEmail($user['email']);
            $new_user->setUsername($user['username']);
            $new_user->setPassword($user['password']);


            echo $user['email']." - ".$user['username']." € - ".$user['password']."\n";   // à remplacer
            $manager->persist($new_user);
            $manager->flush();
        }

    }
}