<?php
namespace App\Widget;

use App\Entity\Timesheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;


class WorkloadTodayWidget implements WidgetInterface
{
    public function getName(): string
    {
        return 'Workload Today';
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
        $workloadToday = $this->getWorkedHoursToday();
        $output =
            '<div class="card">
                    <div class="card-body">
						<h5 class="card-title">workload.today</h5>
						<ul>';
        foreach ($workloadToday as $key => $minutes) {
            $output .= '<li>' . $key . ': ' . $this->toTime($minutes) . '</li>';
        }
        $output .= '</ul>
					</div>
				</div>';
        return $output;
    }
    public function getContext(): array
    {
    }

    public function isForThisUserAvailable(): bool
    {
        return true;
    }

    public function __construct(private EntityManagerInterface $em, private Security $security, private Twig $twig)
    {
    }
    public function getWorkedHoursToday(): iterable
    {
        $workedHours = $this->em->getRepository(Timesheet::class)->getWorkedHoursToday($this->security->getUser());
        return $workedHours;
    }
    public function toTime(int $minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
