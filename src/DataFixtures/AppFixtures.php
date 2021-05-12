<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use DateInterval;
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
        // Alimentation de la table user

        for ($count = 0; $count < 40; $count++) {
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
        // Alimentation de la table event

        $date = new DateTime();

        for ($count = 0; $count < 10; $count++) {
            $event = new Event;

            $newDate = $date->add(new DateInterval('P7D'));
            
            $event->setDate($newDate);
            $event->setName($newDate->format('Y-m-d'));

            if ($count&1) {
                // si $count est impair
                $event->setType(true);
                $event->setName("Entrainement Adulte du ".$newDate->format('d/m/Y'));
            } else {
                // si $count est pair
                $event->setType(false);
                $event->setName("Entrainement Kids du ".$newDate->format('d/m/Y'));
            }
            
            $event->setStartMemberSubs(21);
            $event->setStartAllSubs(14);
            $event->setEndSubs(2);

            $manager->persist($event);
            $manager->flush();
        }

        
    }
}
