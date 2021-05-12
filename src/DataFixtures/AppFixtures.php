<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

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
            $faker = Faker\Factory::create('fr_FR');
            

            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = $faker->freeEmail();

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);

            //encodage et définition du mot de passe
            $password = $this->encoder->encodePassword($user, $firstName);
            $user->setPassword($password);

            $user->setPhone($faker->phoneNumber());
            $user->setBirthDate($faker->dateTimeThisCentury());
            $user->setRoles(["ROLE_USER"]);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
