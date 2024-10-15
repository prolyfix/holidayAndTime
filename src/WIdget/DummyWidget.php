<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Component\Security\Core\Security;
        use Twig\Environment as Twig;

        class DummyWidget implements WidgetInterface
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
                return '<i> TO be deleted </i>';
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
        