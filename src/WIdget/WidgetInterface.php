<?php
namespace App\Widget;
interface WidgetInterface {
    public function getName(): string;
    public function getWidth(): int;
    public function getHeight(): int;
    public function render(): string;
    public function getContext(): array;
}