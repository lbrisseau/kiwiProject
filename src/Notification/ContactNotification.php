<?php
namespace App\Notification;

use App\Entity\Contact;
use App\Entity\Subscription;
use App\Entity\User;
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

    // when a user unsubscribes to an event (or when the admin asks),
    // this email is sent to users whose position has changed in the waiting list
    // $waitingList must be equal to 0 if the user is not in the waiting list anymore
    // or 1 if they are the first in the waiting list, 2 if ... 3 4 etc
    public function waitingList (Subscription $subs, $waitingList)
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