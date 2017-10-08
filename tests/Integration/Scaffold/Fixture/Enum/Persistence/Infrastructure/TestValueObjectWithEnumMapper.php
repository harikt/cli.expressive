<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Enum\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Enum\Domain\TestValueObjectWithEnum;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Enum\Domain\TestValueObjectWithEnum value object mapper.
 */
class TestValueObjectWithEnumMapper extends IndependentValueObjectMapper
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
        $map->type(TestValueObjectWithEnum::class);

        $map->enum(TestValueObjectWithEnum::ENUM)->to('enum')->usingValuesFromConstants();

        $map->enum(TestValueObjectWithEnum::NULLABLE_ENUM)->to('nullable_enum')->usingValuesFromConstants();
    }
}
