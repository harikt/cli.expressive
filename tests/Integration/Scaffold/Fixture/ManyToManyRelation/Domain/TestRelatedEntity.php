<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain;

use Dms\Core\Model\EntityCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestRelatedEntity extends Entity
{
    const PARENT  = 'parent';

    /**
     * @var EntityCollection|TestEntity[]
     */
    public $parent;

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->parent)->asType(TestEntity::collectionType());
    }
}
