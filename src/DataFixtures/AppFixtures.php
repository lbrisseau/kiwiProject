<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($count = 0; $count < 20; $count++) {
            $user = new User;
            $user->setEmail("toto".$count."@toto.com");

            //encodage et définition du mot de passe "toto"
            $password = $this->encoder->encodePassword($user, "toto");
            $user->setPassword($password);

            $user->setFirstName("Toto ".$count);
            $user->setLastName("Toto");
            $user->setPhone("123456");
            $user->setBirthDate(new DateTime());
            $user->setRoles(["ROLE_USER"]);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
