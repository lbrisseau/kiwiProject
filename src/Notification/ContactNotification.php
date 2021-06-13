<?php
namespace App\Notification;

use App\Entity\Contact;
use App\Entity\Event;
use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ContactNotification {

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(MailerInterface $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify (Contact $contact)
    {
        $email = (new Email())
            ->from('contact.auribail@gmail.com')
            ->to('contact.auribail@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($contact->getEmail())
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSubject())
            ->text($contact->getMessage())
            ->html($this->renderer->render('emails/contact.html.twig', [
                'contact' => $contact
            ]));

        $this->mailer->send($email);
    }

    // email sent automatically when a user subscribes to an event
    public function subscription (User $user, $nameEvent, $dateEvent, $waitingList)
    {
        $contact = new Contact();
        $contact->setFirstName($user->getFirstName());
        $contact->setLastName($user->getLastName());
        // when the event is not full, waitingList is equal to -1
        if ($waitingList >= 0)
        {
            $contact->setSubject("En file d'attente pour l'entraînement prochain de motocross");
            $contact->setMessage("Bonjour ".$user->getFirstName()."\n\nL'événement auquel vous avez essayé de vous inscrire (".
            $nameEvent.") est complet.\nVous êtes inscrit.e en numéro ".($waitingList + 1)." en liste d'attente.\nVous serez notifié.e 
            par email dans le cas où une place se libérerait pour vous.\n\nMerci de votre fidélité,\n\nL'équipe de Auribail MX Park");
        }
        else
        {
            $contact->setSubject("Inscription à l'entraînement prochain de motocross");
            $contact->setMessage("Bonjour ".$user->getFirstName().",\n\nVous êtes bien inscrit.e à l'événement prochain : ".
            $nameEvent.".\nNous vous attendons le ".$dateEvent->format('d/m')." au terrain de motocross d'Auribail.
            \n\nMerci de votre fidélité,\n\nL'équipe de Auribail MX Park");
        }
        $email = (new Email())
            ->from('contact.auribail@gmail.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo('contact.auribail@gmail.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSubject())
            ->text($contact->getMessage())
            ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
        ;
        $this->mailer->send($email);
    }

    // email sent automatically when a user unsubscribes to an event
    public function unsubscription (Subscription $subs)
    {
        $user = $subs->getUser();
        $nameEvent = $subs->getEvent()->getName();
        $contact = new Contact();
        $contact->setFirstName($user->getFirstName());
        $contact->setLastName($user->getLastName());
        $contact->setSubject("Désinscription de l'entraînement prochain de motocross");
        $contact->setMessage("Bonjour ".$user->getFirstName().",\n\nVous vous êtes désinscrit.e de l'événement : ".
        $nameEvent.".\n\nNous espérons vous revoir très vite,\n\nL'équipe de Auribail MX Park");
        $email = (new Email())
            ->from('contact.auribail@gmail.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo('contact.auribail@gmail.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSubject())
            ->text($contact->getMessage())
            ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
        ;
        $this->mailer->send($email);
    }

    // when a user unsubscribes to an event,
    // this email is sent to users whose position has changed in the waiting list
    // $waitingList must be equal to 0 if the user is not in the waiting list anymore
    // or 1 if they are the first in the waiting list, 2 if ... 3 4 etc
    public function waitingList (Subscription $subs, SubscriptionRepository $subsRepo)
    {
        $usersSubs = $subsRepo->findAfter($subs);
        foreach ($usersSubs as $thisSub)
        {
            $position = $subsRepo->countOrderSub($thisSub);
            if ($thisSub->getEvent()->getType() == true) // if it is a normal event
            {
                $waitingList = $position[1] - 75;
            }
            else // if it is a kid event
            {
                $waitingList = $position[1] - 15;
            }
            if ($waitingList >= 0)
            {
                $user = $subs->getUser();
                $nameEvent = $subs->getEvent()->getName();
                if ($waitingList == 0)
                {
                    $subject = "Vous n'êtes plus sur liste d'attente pour l'événement prochain de motocross !";
                    $message = "Bonjour ".$user->getFirstName().",\n\nVous êtes inscrit.e pour de bon à l'événement : ".
                    $nameEvent.".\nSi vous ne pouvez plus venir, n'oubliez-pas de vous désinscrire au plus vite sur notre site.
                    \n\nMerci de votre fidélité,\n\nL'équipe de Auribail MX Park";
                }
                else
                {
                    $subject = "Votre position en liste d'attente a évolué pour l'événement prochain de motocross !";
                    $message = "Bonjour ".$user->getFirstName().",\n\nVous êtes désormais numéro ".$waitingList." en liste d'attente pour l'événement : ".
                    $nameEvent.".\nSi vous ne pouvez plus venir, vous pouvez toujours vous désinscrire sur notre site.
                    \n\nMerci de votre fidélité,\n\nL'équipe de Auribail MX Park";
                }
                $contact = new Contact();
                $contact->setFirstName($user->getFirstName());
                $contact->setLastName($user->getLastName());
                $contact->setSubject($subject);
                $contact->setMessage($message);
                $email = (new Email())
                    ->from('contact.auribail@gmail.com')
                    ->to($user->getEmail())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    ->replyTo('contact.auribail@gmail.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject($contact->getSubject())
                    ->text($contact->getMessage())
                    ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
                ;
                $this->mailer->send($email);
            }
        }
    }

    // when the subscriptions are closed for an event,
    // this email is sent to suscribed users to notify them of their new subscription status
    public function finalSubs (Event $event, SubscriptionRepository $subsRepo)
    {
        $allSubs = $subsRepo->findByEvent($event);
        // each validated subscription will increment the count until the limit is reached
        $count = 0;
        if ($event->getType() == true) // if it is a normal event
        {
            $limit = 75;
        }
        else // if it is a kid event
        {
            $limit = 15;
        }
        foreach ($allSubs as $thisSub)
        {
            $user = $thisSub->getUser();
            $nameEvent = $event->getName();
            // case 1: licence OK and position in the list OK
            if ($count < $limit && $thisSub->getValidationState() == true)
            {
                $subject = "Vous êtes définitivement inscrit.e à l'événement prochain de motocross !";
                $message = "Bonjour ".$user->getFirstName().",\n\nVous êtes définitivement inscrit.e à l'événement : ".
                $nameEvent.".\nLes inscriptions et désinscriptions sont closes. Si vous avez un empêchement, merci d'appeler le club.
                \n\nÀ très vite,\n\nL'équipe de Auribail MX Park";
                $count += 1;
            }
            // case 2: licence not OK
            else if ($thisSub->getValidationState() == false)
            {
                $subject = "Vous n'êtes plus inscrit.e à l'événement prochain de motocross";
                $message = "Bonjour ".$user->getFirstName().",\n\nVous n'avez pas renseigné votre numéro de licence à temps.\n
                Les inscriptions sont closes, vous êtes donc désinscrit.e de l'événement : ".
                $nameEvent.".\n\nEn espérant vous revoir très vite,\n\nL'équipe de Auribail MX Park";
            }
            // case 3: all that is left: licence OK but not position in the list
            else
            {
                $subject = "Vous n'êtes plus inscrit.e à l'événement prochain de motocross";
                $message = "Bonjour ".$user->getFirstName().",\n\nLes inscriptions et désinscriptions sont closes pour l'événement : ".
                $nameEvent."\nVous étiez toujours sur liste d'attente, vous n'êtes donc plus inscrit.e à l'événement.
                \n\nEn espérant vous revoir très vite,\n\nL'équipe de Auribail MX Park";
            }
            $contact = new Contact();
            $contact->setFirstName($user->getFirstName());
            $contact->setLastName($user->getLastName());
            $contact->setSubject($subject);
            $contact->setMessage($message);
            $email = (new Email())
                ->from('contact.auribail@gmail.com')
                ->to($user->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->replyTo('contact.auribail@gmail.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($contact->getSubject())
                ->text($contact->getMessage())
                ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
            ;
            $this->mailer->send($email);
        }
    }

    // this email is sent after the admin click on the correct button
    // it warns users who are subscribed to an event to fill their licence number
    public function checkLicence (Event $event, SubscriptionRepository $subsRepo)
    {
        $allSubs = $subsRepo->findByEventLicence($event);
        foreach ($allSubs as $thisSub)
        {
            $user = $thisSub->getUser();
            $nameEvent = $event->getName();
            $contact = new Contact();
            $contact->setFirstName($user->getFirstName());
            $contact->setLastName($user->getLastName());
            $contact->setSubject("Veuillez compléter votre compte avant le prochain événement");
            $contact->setMessage("Bonjour ".$user->getFirstName().",\n\nVotre inscription à l'événement :\n".
            $nameEvent."\nne peut être complète tant que vous n'avez pas renseigné votre numéro de licence.\n
            Veuillez le faire avant le ".$event->getDate()->format('d/m').".\n\nEn espérant vous revoir très vite,\n\nL'équipe de Auribail MX Park");
            $email = (new Email())
                ->from('contact.auribail@gmail.com')
                ->to($user->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->replyTo('contact.auribail@gmail.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($contact->getSubject())
                ->text($contact->getMessage())
                ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
            ;
            $this->mailer->send($email);
        }
    }

    // email sent automatically when a user account is deleted
    public function deletedAccount (User $user)
    {
        $contact = new Contact();
        $contact->setFirstName($user->getFirstName());
        $contact->setLastName($user->getLastName());
        $contact->setSubject("Votre compte a été supprimé");
        $contact->setMessage("Bonjour ".$user->getFirstName().",\n\nVotre compte sur le site Auribail MX Park a été supprimé.
        \n\nBonne continuation,\n\nL'équipe de Auribail MX Park");
        $email = (new Email())
            ->from('contact.auribail@gmail.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo('contact.auribail@gmail.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSubject())
            ->text($contact->getMessage())
            ->html($this->renderer->render('emails/classic.html.twig', ['contact' => $contact]))
        ;
        $this->mailer->send($email);
    }
}