<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Widget\WidgetInterface;

#[AsCommand(
    name: 'make:widget',
    description: 'Create a widget for HolidayAndTime App',
)]
class MakeWidgetCommand extends Command
{
    public function __construct()
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

        $question = $io->ask('What is the name of the widget? (e.g. CalendarWidget)');
        $widgetName = $question;
        $widgetClassContent = "<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Component\Security\Core\Security;
        use Twig\Environment as Twig;

        class $widgetName implements WidgetInterface
        {
            public function getName(): string
            {
                return 'Project Review';
            }
            public function getWidth(): int
            {
                return 12;
            }
            public function getHeight(): int
            {
                return 3;
            }
            public function render(): string
            {
            }
            public function getContext(): array
                        {
            }

            public function isForThisUserAvailable(): bool
                        {
            }

            public function __construct(private EntityManagerInterface \$em,private Security \$security, private Twig \$twig)
            {
            }

        }
        ";

        file_put_contents(__DIR__ . "/../Widget/{$widgetName}.php", $widgetClassContent);

        $io->success('You just created a widget!');

        return Command::SUCCESS;
    }
}
