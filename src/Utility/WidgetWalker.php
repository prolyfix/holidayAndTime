<?php
namespace Utility;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;


class WidgetWalker
{
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
                    $widgetClasses[] = $className;
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
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j] === '{') {
                        $className = $tokens[$i + 2][1];
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