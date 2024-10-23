<?php
        namespace App\Widget;
        use Doctrine\ORM\EntityManagerInterface;
        use Symfony\Component\Security\Core\Security;
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
                return 			
                '<div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                        {{ render_chart(chart) }}
                            <h5 class="card-title">Overtime Development</h5>
                            <a href="#" class="btn btn-primary">Add Time</a>
                        </div>
                    </div>
                </div>';
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
        