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
                $start = new \DateTime('today 08:00:00');
                $end = new \DateTime('today 19:00:00');
                
                $output = [];
                while ($start < $end) {
                    $output[$start->format('H:i')] = null;
                    $start->modify('+15 minutes');
                }

                foreach ($timesheets as $timesheet) {
                    $quarterHour = (int) $timesheet->getStartTime()->format('i') / 15;
                    $minuteString = substr("0".$quarterHour*15,-2);
                
                    $output[$timesheet->getStartTime()->format('H').':'.$minuteString][] = $timesheet;
                }

                dump($output);
                return $this->twig->render('widget/day_view_widget.html.twig', [
                    'journal' =>$output,
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
        