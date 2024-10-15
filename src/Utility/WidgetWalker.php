<?php
namespace App\Utility;

use App\Widget\WidgetInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Twig\Environment as Twig;
use Symfony\Component\Security\Core\Security;

class WidgetWalker
{

    public function __construct(private EntityManagerInterface $em, private Security $security, private Twig $twig)
    {
    }

    public function findWidgetClasses(string $directory): array
    {
        $widgetClasses = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $this->getClassNameFromFile($file->getPathname());
                if ($className && $this->implementsWidgetInterface($className)) {
                    $widgetClasses[] = new $className($this->em, $this->security, $this->twig);
                }
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
                    if ($tokens[$j][0] === 316) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j][0] === 316 || $tokens[$j][0] === 313 && $className === '') {
                        $className = $tokens[$j][1];
                        break;
                    }
                }
            }
        }
        return $namespace && $className ? $namespace . '\\' . $className : null;
    }

    private function implementsWidgetInterface(string $className): bool
    {
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->implementsInterface(WidgetInterface::class);
    }
}