<?php

namespace App\Command;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:first-user',
    description: 'Create the first user',
)]
class FirstUserCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if(count($users) > 0){
            $io->error('There are already users in the database');
            return Command::FAILURE;
        }
        $company = new Company();
        $user = new User();
        $user->setCompany($company);    
        $question = $io->ask('Enter the email of the first user');
        $user->setEmail($question);
        $plainPassword = $io->ask('Enter the password of the first user');
        $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $io->success('First user created successfully!');

        return Command::SUCCESS;
    }
}
