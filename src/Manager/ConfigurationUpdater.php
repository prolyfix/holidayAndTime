<?php
namespace App\Manager;

use App\Event\ModifiableArrayEvent;
use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConfigurationUpdater
{
    public function __construct(private Kernel $kernel,private  EntityManagerInterface $em,private  Security $security)
    {
    }
    public function getConfigurationList(): array
    {
        $bundles = $this->kernel->getBundles();
        foreach($bundles as $bundle)
        {
            if (defined(get_class($bundle) . '::IS_MODULE')) {
                
        }
    }
        return [];
    }

    public function updateConfigurationList(array $list): void
    {
    }

}