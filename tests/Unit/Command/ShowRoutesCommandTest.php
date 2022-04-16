<?php

namespace Rodrifarias\SlimRouteAttributes\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Rodrifarias\SlimRouteAttributes\Command\ShowRoutesCommand;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ShowRoutesCommandTest extends TestCase
{
    public function testShouldShowOutputTableWithRoutes(): void
    {
        $app = new Application();
        $app->add(new ShowRoutesCommand(new ScanRoutes()));

        $command = $app->find('show-routes');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['--path' => 'tests']);

        $expectedString = '+------------------------------+-------------+----------------------------+----------+
| Route                        | Http Method | Controller Method          | IsPublic |
+------------------------------+-------------+----------------------------+----------+
| /home                        | GET         | HomeController:showAll     | Yes      |
| /home                        | POST        | HomeController:create      | No       |
| /home/map/test               | POST        | HomeController:mapTest     | No       |
| /home/map/test               | GET         | HomeController:mapTest     | No       |
| /home/optional[/{id:[0-9]+}] | GET         | HomeController:optional    | No       |
| /home/{id:\d+}               | GET         | HomeController:show        | Yes      |
| /home/{id:\d+}               | PUT         | HomeController:update      | No       |
| /home/{id:\d+}               | DELETE      | HomeController:delete      | No       |
| /home/{id:\d+}               | PATCH       | HomeController:updatePatch | No       |
+------------------------------+-------------+----------------------------+----------+
';
        $this->assertEquals($expectedString, $commandTester->getDisplay());
    }
}
