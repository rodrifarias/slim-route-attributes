<?php

namespace Rodrifarias\SlimRouteAttributes\Route\Scan;

use Exception;
use Psr\Http\Server\MiddlewareInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Rodrifarias\SlimRouteAttributes\Attributes\Validate\AttributesValidate;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Exception\MiddlewareShouldImplementsMiddlewareInterfaceException;
use Rodrifarias\SlimRouteAttributes\Route\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class ScanRoutes implements ScanRoutesInterface
{
    /**
     * @throws DirectoryNotFoundException
     * @throws ReflectionException
     * @return Route[]
     */
    public function getRoutes(string $path, bool $fromCache = false): array
    {
        if (!is_dir($path)) {
            throw new DirectoryNotFoundException($path);
        }

        if (!$fromCache) {
            $files = $this->getFilesScan($path);
            return $this->scanClassFiles($files);
        }

        $cacheSystem = new FilesystemAdapter();

        return $cacheSystem->get('scan-app-routes', function (ItemInterface $item) use ($path) {
            $item->expiresAfter(10000);
            $files = $this->getFilesScan($path);
            return $this->scanClassFiles($files);
        });
    }

    /**
     * @return string[]
     */
    private function getFilesScan(string $path): array
    {
        $files = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $it->rewind();

        while ($it->valid()) {
            $pathFile = str_replace('\\\\', '\\', $it->key());
            $pathFile = str_replace('//', '/', $pathFile);
            $isValidFile = $this->isValidFile($pathFile, $it->isDot());

            if ($isValidFile) {
                $filename = pathinfo($it->getSubPathName(), PATHINFO_FILENAME);
                $files[] = $this->extractNamespaceFile($pathFile) . '\\' . $filename;
            }

            $it->next();
        }

        return $files;
    }

    private function isValidFile(string $pathFile, mixed $isDot): bool
    {
        $isPhpFile = str_ends_with($pathFile, '.php');
        $isDependencyPath = str_contains($pathFile, 'vendor') || str_contains($pathFile, 'node_modules');

        return !$isDot && file_exists($pathFile) && $isPhpFile && !$isDependencyPath;
    }

    private function extractNamespaceFile(string $file): string
    {
        $ns = '';
        $handler = fopen($file, "r");

        if ($handler) {
            while (($line = fgets($handler)) !== false) {
                if (str_starts_with($line, 'namespace')) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handler);
        }

        return $ns;
    }

    /**
     * @throws ReflectionException
     * @return Route[]
     */
    private function scanClassFiles(array $class): array
    {
        $routes = [];

        foreach ($class as $cla) {
            try {
                $reflectionClass = new ReflectionClass($cla);

                if (!$reflectionClass->getAttributes()) {
                    continue;
                }

                $prefix = $this->prefixRoute($reflectionClass);

                if (!$prefix) {
                    continue;
                }

                foreach ($reflectionClass->getMethods() as $method) {
                    if ($method->getAttributes()) {
                        $infoRoute = $this->attributesMethodRoute($method);
                        $hasMapAndHttpMethod = $infoRoute['mapRoutes'] && array_key_exists('httpMethod', $infoRoute);

                        if ($hasMapAndHttpMethod) {
                            continue;
                        }

                        foreach ($infoRoute['mapRoutes'] as $mapRoute) {
                            $routes[] = new Route(
                                $reflectionClass->getName(),
                                $infoRoute['classMethod'],
                                strtolower($mapRoute),
                                $prefix . $infoRoute['path'],
                                $infoRoute['publicAccess'],
                                $infoRoute['middleware'],
                            );
                        }

                        if (array_key_exists('httpMethod', $infoRoute)) {
                            $routes[] = new Route(
                                $reflectionClass->getName(),
                                $infoRoute['classMethod'],
                                strtolower($infoRoute['httpMethod']),
                                $prefix . $infoRoute['path'],
                                $infoRoute['publicAccess'],
                                $infoRoute['middleware'],
                            );
                        }
                    }
                }
            } catch (Exception $exception) {
                if ($exception instanceof MiddlewareShouldImplementsMiddlewareInterfaceException) {
                    throw $exception;
                }
            }
        }

        return $routes;
    }

    private function attributesMethodRoute(ReflectionMethod $reflectionMethod): array
    {
        $getNameMethodHttp = function (string $classNamespace): string {
            $namespaceArray = explode('\\', $classNamespace);
            return strtolower(array_pop($namespaceArray));
        };

        $infoRoute = [
            'middleware' => [],
            'publicAccess' => false,
            'classMethod' => $reflectionMethod->getName(),
            'mapRoutes' => [],
        ];

        foreach ($reflectionMethod->getAttributes() as $attribute) {
            $arguments = $attribute->getArguments();

            if (AttributesValidate::isMethodHttp($attribute->getName())) {
                $infoRoute['httpMethod'] = $getNameMethodHttp($attribute->getName());
                $infoRoute['path'] = $arguments ? $arguments[0] : '';
                continue;
            }

            if (AttributesValidate::isPublicAccess($attribute->getName())) {
                $infoRoute['publicAccess'] = $arguments ? $arguments[0] : '';
                continue;
            }

            if (AttributesValidate::isMiddleware($attribute->getName())) {
                $middlewares = $arguments[0];

                $middlewaresImplementsMiddlewareInterface = array_filter($middlewares, function ($m) {
                    $isMiddlewareInterface = false;
                    $instanceofMiddlewareInterface = $m instanceof MiddlewareInterface;

                    if (is_string($m) && class_exists($m)) {
                        $reflectionClass = new ReflectionClass($m);
                        $isMiddlewareInterface = $reflectionClass->implementsInterface(MiddlewareInterface::class);
                    }

                    if ($isMiddlewareInterface || $instanceofMiddlewareInterface) {
                        return true;
                    }

                    throw new MiddlewareShouldImplementsMiddlewareInterfaceException();
                });

                $infoRoute['middleware'] = $middlewaresImplementsMiddlewareInterface;
                continue;
            }

            if (AttributesValidate::isMapRoutes($attribute->getName())) {
                [$path, $methods] = $arguments;
                $infoRoute['path'] = $path ?: '';

                foreach ($methods as $method) {
                    if (AttributesValidate::isMethodHttp($method)) {
                        $infoRoute['mapRoutes'][] = $method;
                    }
                }
            }
        }

        return $infoRoute;
    }

    private function prefixRoute(ReflectionClass $reflectionClass): ?string
    {
        $attributes = $reflectionClass->getAttributes();

        foreach ($attributes as $attribute) {
            $className = $attribute->getName();
            $classNameIsRoute = str_ends_with($className, 'Route');
            $isValidArgument = $attribute->getArguments() && is_string($attribute->getArguments()[0]);
            $isValidAttribute = $classNameIsRoute && $isValidArgument;

            if ($isValidAttribute) {
                return $attribute->getArguments()[0];
            }
        }

        return null;
    }
}
