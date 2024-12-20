<?php

namespace App\Utility;

use App\Kernel;
use App\Module\ModuleInterface;
use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment as Twig;


class ModuleWalker
{

    public function __construct(private EntityManagerInterface $em, private Security $security, private Kernel $kernel) {}

    public function findModuleClasses(string $directory): array
    {
        $widgetClasses = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className && $this->implementsModuleInterface($className)) {
                    $widgetClasses[$className::getShortName()] = $className;
                }
            }
        }

        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            if(in_array(ModuleInterface::class,class_implements($bundle)))
            {
                $widgetClasses[$bundle::getShortName()] = $bundle::class;
            }
        }
        return $widgetClasses;
    }

    private function getClassNameFromFile(string $filePath): ?string
    {
        $namespace = '';
        $className = '';

        $tokens = token_get_all(file_get_contents($filePath));
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j][0] === 316 || $tokens[$j][0] == 265) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if (($tokens[$j][0] === 316 || $tokens[$j][0] === 313 || $tokens[$j][0] == 262) && $className === '') {
                        $className = $tokens[$j][1];
                        break;
                    }
                }
            }
        }
        return $namespace && $className ? $namespace . '\\' . $className : null;
    }

    private function implementsModuleInterface(string $className): bool
    {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->implementsInterface(ModuleInterface::class);
    }
}
