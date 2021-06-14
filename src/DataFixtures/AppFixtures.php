<?php

namespace App\DataFixtures;

use App\Entity\Animation;
use App\Entity\Event;
use App\Entity\Settings;
use App\Entity\Subscription;
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

        $nbUsers = 100;
        $nbEvents = 5;

        // Initialisation des settings
        $settings = new Settings;
        $settings->setDefaultStartAllSubs('14');
        $settings->setDefaultStartMemberSubs('21');
        $settings->setDefaultEndSubs('2');
        $manager->persist($settings);
        $manager->flush();

        // Initialisation Animation
        $animation = new Animation;
        $animation->setCarouselMessage1("Clôture imminente des inscriptions pour la session Kids du 22/06 !");
        $animation->setCarouselMessage2("Attention les sessions d'entrainement sont susceptibles d'être annulées en cas d'intempéries. Surveillez-bien vos mails !");
        $animation->setCarouselMessage3("Envie de rejoindre la dream team des pilotes du MX Park ? Allez hop hop hop, on s'inscrit !");
        $animation->setCarouselMessage4("Je ne sais plus quoi dire, alors je vais sourire :)");
        $manager->persist($animation);
        $manager->flush();

        // Creation d'un user Admin

        $user = new User;
        $faker = Faker\Factory::create('fr_FR');

        $firstName = 'admin';
        $lastName = 'ADMIN';
        $email = 'admin@admin.com';

        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);

        //encodage et définition du mot de passe (=prenom)
        $password = $this->encoder->encodePassword($user, $firstName);
        $user->setPassword($password);

        $user->setPhone($faker->phoneNumber());
        $user->setBirthDate($faker->dateTimeThisCentury());
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();

        // Alimentation de la table event

        $tabEvent = []; //tableau des Event créés sera utile pour remplissage de la table subscription

        $date = new DateTime();

        for ($count = 0; $count < $nbEvents; $count++) {
            $event = new Event;

            $newDate = $date->add(new DateInterval('P7D'));

            $event->setDate($newDate);
            if ($count & 1) {
                // si $count est impair
                $event->setType(true);
                $event->setName("Entrainement Adulte du " . $newDate->format('d/m/Y'));
            } else {
                // si $count est pair
                $event->setType(false);
                $event->setName("Entrainement Kids du " . $newDate->format('d/m/Y'));
            }

            $event->setStartMemberSubs(21);
            $event->setStartAllSubs(14);
            $event->setEndSubs(2);

            $manager->persist($event);
            $tabEvent[] = $event;
            $manager->flush();
        }


        // Alimentation de la table user

        $tabUser = []; //tableau des user créés sera utile pour remplissage de la table subscription

        for ($count = 0; $count < $nbUsers; $count++) {
            $user = new User;
            $faker = Faker\Factory::create('fr_FR');


            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $email = $faker->freeEmail();

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);

            //encodage et définition du mot de passe (=prenom)
            $password = $this->encoder->encodePassword($user, $firstName);
            $user->setPassword($password);

            $user->setPhone($faker->phoneNumber());
            $user->setBirthDate($faker->dateTimeThisCentury());
            $user->setRoles(["ROLE_USER"]);
            $aleat = rand(1, 10);
            if ($aleat > 2) {
                $user->setLicenceNumber('28539567');
            }
            $manager->persist($user);

            $tabUser[] = $user;
        }
        $manager->flush();

        // Alimentation de la table Subscription
        $dateSub = new DateTime('now');

        for ($count = 0; $count < $nbUsers; $count++) {
            $sub = new Subscription;
            $user = $tabUser[$count];
            $indexEvent = count($tabEvent) - 1;
            $event = $tabEvent[rand(0, $indexEvent)];
            $sub->setEvent($event);
            $sub->setUser($user);
            $newDateSub = $dateSub->add(new DateInterval('PT30S'));
            $sub->setSubsDate($newDateSub);
            $licence = $user->getLicenceNumber();
            if (isset($licence)) {
                $sub->setValidationState(true);
            } else {
                $sub->setValidationState(false);
            }
            $manager->persist($sub);
            $manager->flush();
        }
    }
}
