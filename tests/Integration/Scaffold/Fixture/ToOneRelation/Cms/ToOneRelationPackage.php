<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Cms;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Cms\Modules\TestEntityModule;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Cms\Modules\TestRelatedEntityModule;
use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;

/**
 * The ToOneRelation package.
 */
class ToOneRelationPackage extends Package
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
        $package->name('ToOneRelation');

        $package->metadata([
            'icon' => '',
        ]);

        $package->modules([
            'test-entity' => TestEntityModule::class,
            'test-related-entity' => TestRelatedEntityModule::class,
        ]);
    }
}
