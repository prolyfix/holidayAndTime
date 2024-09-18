<?php

namespace App\Command;

use App\Entity\Company;
use App\Entity\Configuration;
use App\Entity\TypeOfAbsence;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-default-properties',
    description: 'update default properties for all companies',
)]
class UpdateDefaultPropertiesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private UserFactory $userFactory)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $companies = $this->em->getRepository(Company::class)->findAll();
        foreach($companies as $company){
            foreach($this->userFactory::COMPANY_DEFAULT_TYPE_OF_ABSENCES as $key=>$value){
                $exist = $this->em->getRepository(TypeOfAbsence::class)->findOneBy(['company'=>$company,'name'=>$key]);
                if($exist){
                    continue;
                }
                $typeOfAbsence = new TypeOfAbsence();
                $typeOfAbsence->setCompany($company);
                $typeOfAbsence->setName($key);
                $typeOfAbsence->setIsHoliday($value['isHoliday']);
                $typeOfAbsence->setIsTimeHoliday($value['isTimeHoliday']);
                $typeOfAbsence->setHasToBeValidated($value['hasToBeValidated']);
                $typeOfAbsence->setIsBankHoliday($value['isBankHoliday']);
                $typeOfAbsence->setIsWorkingDay($value['isWorkingDay']);
                $this->em->persist($typeOfAbsence);
            }
            foreach (UserFactory::COMPANY_CONFIGURATIONS as $name => $type) {
                $exists = $this->em->getRepository(Configuration::class)->findOneBy(['company'=>$company,'name'=>$name]);
                if($exists){
                    continue;
                }
                $configuration = new Configuration();
                $configuration->setType($type);
                $configuration->setName($name); 
                $configuration->setCompany($company);
                $company->addConfiguration($configuration);
            }
        }
        $this->em->flush();
        $io->success('All companies have been updated with default properties');
        return Command::SUCCESS;
    }
}
