<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestEntity;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestRelatedEntity;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestEntity entity mapper.
 */
class TestEntityMapper extends EntityMapper
{
    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(TestEntity::class);
        $map->toTable('test_entities');

        $map->idToPrimaryKey('id');

        $map->relation(TestEntity::RELATED)
            ->to(TestRelatedEntity::class)
            ->toOne()
            ->identifying()
            ->withBidirectionalRelation(TestRelatedEntity::PARENT)
            ->withParentIdAs('test_entity_id');
    }
}
