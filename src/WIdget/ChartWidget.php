<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Bundle\SecurityBundle\Security;
        use Twig\Environment as Twig;

        class ChartWidget implements WidgetInterface
        {
            public function getName(): string
            {
                return 'Chart Widget';
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
                return 	$this->twig->render('widget/chart.html.twig')		;
;
            }
            public function getContext(): array
            {
                return [];
            }

            public function isForThisUserAvailable(): bool
            {
                return true;
            }

            public function __construct(private EntityManagerInterface $em,private Security $security, private Twig $twig)
            {
            }

        }
        