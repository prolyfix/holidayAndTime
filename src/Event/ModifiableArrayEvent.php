<?php 
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ModifiableArrayEvent extends Event
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addItem(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function removeItem(string $key): void
    {
        unset($this->data[$key]);
    }
}
