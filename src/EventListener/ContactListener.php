<?php
namespace App\EventListener;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ContactListener
{
    public function __construct(private EntityManagerInterface $em, private ParameterBagInterface $params, private MailerInterface $mailer,)
    {
    }

    public function postPersist(Contact $contact, PostPersistEventArgs $event): void
    {
        if($contact->getIsUser()){
            $this->em->getRepository(User::class)->findOneByEmail($contact->getEmail());
            if($contact->getUser() !== null){
                $contact->setUser($contact->getUser());
                return;
            }
            $user = new User();
            $user->setEmail($contact->getEmail());
            $user->setContact($contact);
            $user->setRoles([User::ROLE_GAST]);
            $user->setPassword(uniqid());

            $this->em->persist($user);
            $this->em->flush();
            $email = new TemplatedEmail();
            $email->from(new Address($this->params->get('email_sender'), $this->params->get('email_sender_name')))
            ->to($user->getEmail())
            ->subject('Invitation à l\'utilisation des services de la plateforme')
            ->htmlTemplate('email/registration.html.twig')
            ->context([
                'user' => $user
            ]);
            $this->mailer->send($email);
        }
    }
    public function preUpdate(Contact $contact, PreUpdateEventArgs $event): void
    {
        if($contact->getIsUser()){
            $this->em->getRepository(User::class)->findOneByEmail($contact->getEmail());
            if($contact->getUser() !== null){
                $contact->setUser($contact->getUser());
                return;
            }
            $user = new User();
            $user->setEmail($contact->getEmail());
            $user->setContact($contact);
            $user->setPassword(uniqid());
            $user->setRoles([User::ROLE_GAST]);

            $this->em->persist($user);
            $this->em->flush();
            $email = new TemplatedEmail();
            $email->from(new Address($this->params->get('email_sender'), $this->params->get('email_sender_name')))
            ->to($user->getEmail())
            ->subject('Invitation à l\'utilisation des services de la plateforme')
            ->htmlTemplate('email/registration.html.twig')
            ->context([
                'user' => $user
            ]);
        }
    }

}