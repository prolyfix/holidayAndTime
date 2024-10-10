<?php
namespace App\Widget;

class NumberOfUserWidget implements WidgetInterface
{

    public function getName(): string
    {
        return 'Number of Users';
    }
    public function getWidth(): int
    {
        return 1;
    }
    public function getHeight(): int
    {
        return 1;
    }
    public function render(): string
    {
        return '<div class="card widget" style="background-color: #f8f9fa; color: #000000; font-size: 1.5em; text-align: center; padding-top: 10px; padding-bottom: 10px;" data-widget-target="card" id="widget_'.self::class.'" data-widgetId="'.self::class.'" data-width="190%"></div>';
    }
    public function getContext(): array
    {
        return [];
    }
}