<?php

namespace Dms\Cli\Expressive\Tests\Integration;

use Dms\Cli\Expressive\Tests\Integration\Fixtures\Demo\DemoFixture;
use Illuminate\Contracts\Console\Kernel;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RouteListCommandTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }

    public function testRouteList()
    {
        $this->app[Kernel::class]->call('route:list');
    }
}
