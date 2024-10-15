<?php
        namespace App\Widget;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Component\Security\Core\Security;

        class WorkingTodayWidget implements WidgetInterface
        {
            public function getName(): string
            {
                return 'Working Today';
            }
            public function getWidth(): int
            {
                return 4;
            }
            public function getHeight(): int
            {
                return 3;
            }
            public function render(): string
            {
                $output =  
                '<div class="card"><div class="card-body">
                    <div class="card-body">
						<h5 class="card-title">workingToday</h5>
						<ul>';
                $todayWorkers = $this->todaysWorkers($this->em, $this->security->getUser());
                foreach( $todayWorkers as $user){
                    $output .= '<li>'.$user->getName().'</li>';
                }
				$output .= 	'</div>
                </div></div>';
                return $output;
            }
            public function getContext(): array
                        {
            }

            public function isForThisUserAvailable(): bool
            {
                return true;
            }

            public function __construct(private EntityManagerInterface $em, private Security $security)
            {
            }
            private function todaysWorkers(EntityManagerInterface $em, User $user): array
            {
                $output = [];
                $usersRaw = $em->getRepository(User::class)->findByCompany($user->getCompany());
                $today = new \DateTime();
                foreach ($usersRaw as $user) {
                    $weekdayProperties = $user->getRightUserWeekdayProperties($today);
                    foreach ($weekdayProperties as $weekdayProperty) {
                        if ($weekdayProperty->getWeekday() == $today->format('l') && $weekdayProperty->getWorkingHours() !== null) {
                            $output[] = $user;
                        }
                    }
                }
                return $output;
            }
        }
        