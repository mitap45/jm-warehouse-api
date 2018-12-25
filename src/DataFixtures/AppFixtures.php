<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadOrderProducts($manager);
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('company1');
        $hashedPassword = password_hash('company1', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('company2');
        $hashedPassword = password_hash('company2', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('company3');
        $hashedPassword = password_hash('company3', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('cargoA');
        $hashedPassword = password_hash('cargoA', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('cargoB');
        $hashedPassword = password_hash('cargoB', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('cargoC');
        $hashedPassword = password_hash('cargoC', PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);
        $manager->persist($user);

    }

    private function loadOrderProducts(ObjectManager $manager)
    {
        $product = new Product();
        $product->setCode('1001');
        $manager->persist($product);

        $product = new Product();
        $product->setCode('1112');
        $manager->persist($product);

        $product = new Product();
        $product->setCode('1135');
        $manager->persist($product);

        $product = new Product();
        $product->setCode('1240');
        $manager->persist($product);

        $product = new Product();
        $product->setCode('1673');
        $manager->persist($product);

        $product = new Product();
        $product->setCode('1391');
        $manager->persist($product);

    }
}
