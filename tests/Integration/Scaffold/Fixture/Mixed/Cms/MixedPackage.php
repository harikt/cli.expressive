<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Mixed\Cms;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Mixed\Cms\Modules\TestEntityModule;
use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;

/**
 * The Mixed package.
 */
class MixedPackage extends Package
{
    /**
     * Defines the structure of this cms package.
     *
     * @param PackageDefinition $package
     *
     * @return void
     */
    protected function define(PackageDefinition $package)
    {
        $package->name('Mixed');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([
            'test-entity' => TestEntityModule::class,
        ]);
    }
}
