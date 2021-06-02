<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact {
    /**
     * @var string|null
     * @Assert\NotBlank(message="Par quel doux prénom vous faites vous appeler ?")
     * @Assert\Length(min=2,max=100,minMessage="Drôle de prénom ! Je veux plus de 2 caractères.", maxMessage="Drôle de prénom ! Je veux moins de 100 caractères.")
     */
    private $firstName;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Quel est votre petit nom ?")
     * @Assert\Length(min=2,max=100,minMessage="Drôle de nom ! Je veux plus de 2 caractères.", maxMessage="Drôle de nom ! Je veux moins de 100 caractères.")
     */
    private $lastName;

    /**
     * @var string|null
     * @Assert\NotBlank(message="J'ai besoin de votre adresse email !")
     * @Assert\Email(message="J'ai besoin de votre adresse email !")
     */
    private $email;

    /**
     * @var string|null
     * @Assert\NotBlank(message="Pourquoi souhaitez-vous nous contacter ?")
     * @Assert\Length(min=2,max=100,minMessage="Soyez concis.e, mais pas trop : entre 2 et 100 caractères.", maxMessage="Soyez concis.e, mais pas trop : entre 2 et 100 caractères.")
     */
    private $subject;

    /**
     * @var string|null
     * @Assert\NotBlank(message="La concision est une qualité, mais là il y a de l'abus.")
     * @Assert\Length(min=10,minMessage="La concision est une qualité, mais là il y a de l'abus.")
     */
    private $message;
    


    /**
     * Get the value of firstName
     *
     * @return  string|null
     */ 
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @param  string|null  $firstName
     *
     * @return  self
     */ 
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     *
     * @return  string|null
     */ 
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @param  string|null  $lastName
     *
     * @return  self
     */ 
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string|null
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string|null  $email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of subject
     *
     * @return  string|null
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param  string|null  $subject
     *
     * @return  self
     */ 
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of message
     *
     * @return  string|null
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @param  string|null  $message
     *
     * @return  self
     */ 
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}