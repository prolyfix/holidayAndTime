<?php
        namespace App\Widget;
        use App\Entity\Timesheet;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Component\Security\Core\Security;
        use Twig\Environment as Twig;


        class DayViewWidget implements WidgetInterface
        {
            public function getName(): string
            {
                return 'DayViewWidget';
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

                $timesheets = $this->em->getRepository(Timesheet::class)->getTimesheetsForUserBetweenStartAndEnd($this->security->getUser(), new \DateTime('today midnight'), new \DateTime('tomorrow midnight'));
                    
                return $this->twig->render('widget/day_view_widget.html.twig', [
                    'day' => date('l'),
                    'date' => date('d-m-Y'),
                    'time' => date('H:i:s')
                ]);
            }
            public function getContext(): array
            {
            }

            public function isForThisUserAvailable(): bool
            {
                return true;
            }

            public function __construct(private EntityManagerInterface $em,private  Security $security, private Twig $twig)
            {
            }

        }
        