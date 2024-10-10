<?php
namespace App\Widget;
use App\Widget\WidgetInterface;

class DummyWidget implements WidgetInterface {

    public function __construct() {
    }


    public function getName(): string {
        return 'Dummy Widget';
    }

    public function getWidth(): int {
        return 6;
    }

    public function getHeight(): int {
        return 3;
    }

    public function render(): string {
        return '<div class="card"><div class="card-body">This is a dummy widget</div></div>';
    }

    public function getContext(): array {
        return [];
    }
}