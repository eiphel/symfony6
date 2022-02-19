<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();

        $user->setEmail('test@test.com');
        $user->setGender('M');
        $user->setFirstname('Benoît');
        $user->setLastname('Benoît');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setToken(sha1(base64_encode(random_bytes(50))));

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'test'
        ));

        $manager->persist($user);

        $manager->flush();
    }
}
