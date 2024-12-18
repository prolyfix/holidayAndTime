<?php
namespace App\Widget;

use App\Entity\Timesheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment as Twig;

class WeekWorkingTimeWidget implements WidgetInterface
{
    public function getName(): string
    {
        return 'Week Working Time';
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

        $workingTimeThisWeek = $this->security->getUser()->getRightUserWeekdayProperties(new \DateTime());
        $timesheetThisWeek = $this->em->getRepository(Timesheet::class)->getWeekTimesheet($this->security->getUser());
        return
            '<div class="card"><div class="card-body">
                    <h2>Week Working Time</h2>
                    				<table class="table datagrid">
					<thead>
						<tr>
							<th>Monday</th>
							<th>Tuesday</th>
							<th>Wednesday</th>
							<th>Thursday</th>
							<th>Friday</th>
							<th>Saturday</th>
							<th>Sunday</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>' . ((isset($workingTimeThisWeek[0]) && $workingTimeThisWeek[0]->getWorkingHours() !== null) ? $workingTimeThisWeek[0]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[1]) && $workingTimeThisWeek[1]->getWorkingHours() !== null) ? $workingTimeThisWeek[1]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[2]) &&  $workingTimeThisWeek[2]->getWorkingHours() !== null) ? $workingTimeThisWeek[2]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[3]) && $workingTimeThisWeek[3]->getWorkingHours() !== null) ? $workingTimeThisWeek[3]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[4]) && $workingTimeThisWeek[4]->getWorkingHours() !== null) ? $workingTimeThisWeek[4]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[5]) && $workingTimeThisWeek[5]->getWorkingHours() !== null) ? $workingTimeThisWeek[5]->getWorkingHours()->format('h:i') : '') . '</td>
							<td>' . ((isset($workingTimeThisWeek[6]) && $workingTimeThisWeek[6]->getWorkingHours() !== null) ? $workingTimeThisWeek[6]->getWorkingHours()->format('h:i') : '') . '</td>
						</tr>
						<tr>
							<td>' . (isset($timesheetThisWeek['Monday']) ? $this->toTime($timesheetThisWeek['Monday']) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Tuesday']) ? $this->toTime(($timesheetThisWeek['Tuesday'])) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Wednesday']) ? $this->toTime(($timesheetThisWeek['Wednesday'])) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Thursday']) ? $this->toTime(($timesheetThisWeek['Thursday'])) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Friday']) ? $this->toTime(($timesheetThisWeek['Friday'])) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Saturday']) ? $this->toTime(($timesheetThisWeek['Saturday'])) : '') . '</td>
							<td>' . (isset($timesheetThisWeek['Sunday']) ? $this->toTime(($timesheetThisWeek['Sunday'])) : '') . '</td>
						</tr>						
					</tbody>
				</table>
                </div></div>';
    }
    public function getContext(): array
    {

    }

    public function isForThisUserAvailable(): bool
    {
        return true;
    }

    public function __construct(private EntityManagerInterface $em, private Security $security, Twig $twig)
    {
    }

    public function toTime($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

}
