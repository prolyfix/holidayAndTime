<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Bundle\SecurityBundle\Security;
        use Twig\Environment as Twig;

        class HolidayStatementWidget implements WidgetInterface
        {
            public function getName(): string
            {
                return 'Holiday Statement';
            }
            public function getWidth(): int
            {
                return 4;
            }
            public function getHeight(): int
            {
                return 1;
            }
            public function render(): string
            {
                return 
                '<div class="card"><div class="card-body">
                    <h2>Holiday statement</h2>
                    <i> To be implemented</i>
                </div></div>';
            }
            public function getContext(): array
            {
                return [];
            }

            public function isForThisUserAvailable(): bool
            {
                return true;
            }

            public function __construct(private EntityManagerInterface $em, Security $security, private Twig $twig)
            {
            }

        }
        