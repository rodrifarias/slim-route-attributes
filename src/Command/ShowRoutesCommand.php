<?php

namespace Rodrifarias\SlimRouteAttributes\Command;

use InvalidArgumentException;
use ReflectionException;
use Rodrifarias\SlimRouteAttributes\Exception\DirectoryNotFoundException;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutesInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowRoutesCommand extends Command
{
    public function __construct(private ScanRoutesInterface $scanRoutes, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('show-routes')
            ->setDescription('Show all routes registered')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to scan routes');
    }

    /**
     * @throws ReflectionException
     * @throws DirectoryNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getOption('path');

        if (!$path) {
            throw new InvalidArgumentException('The option --path is required');
        }

        $path = getcwd() . '/' . $path;
        $rows = $this->getRowsRoutes($path);

        $table = new Table($output);
        $table->setHeaders(['Route', 'Http Method', 'Controller Method', 'IsPublic']);
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @throws ReflectionException
     * @throws DirectoryNotFoundException
     */
    private function getRowsRoutes(string $path): array
    {
        $routes = $this->scanRoutes->getRoutes($path);
        usort($routes, fn ($r1, $r2) => strcmp($r1->path, $r2->path));

        return array_map(fn ($r) => [
            $r->path,
            strtoupper($r->httpMethod),
            $this->getControllerMethodDescription($r->className, $r->classMethod),
            $r->publicAccess ? 'Yes' : 'No'
        ], $routes);
    }

    private function getControllerMethodDescription(string $namespace, string $method): string
    {
        $className = explode('\\', $namespace);
        return end($className) . ':' . $method;
    }
}
