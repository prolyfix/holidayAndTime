<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Bundle\SecurityBundle\Security;
        use Twig\Environment as Twig;

        class PodomoroWidget implements WidgetInterface
        {
            public function getName(): string
            {
                return 'Podomoro Timer';
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
                return $this->twig->render('widget/podomoro.html.twig', [
                    'time' => '25:00',
                    'status' => 'stopped',
                ]); 
            }
            public function getContext(): array
                        {
            }

            public function isForThisUserAvailable(): bool
            {
                return true;
            }

            public function __construct(private EntityManagerInterface $em,private Security $security, private Twig $twig)
            {
            }

        }
        