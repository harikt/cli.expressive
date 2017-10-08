<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestValueObject;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestValueObject value object mapper.
 */
class TestValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestValueObject::class);

        $map->property(TestValueObject::STRING)->to('string')->asVarchar(255);

        $map->property(TestValueObject::INT)->to('int')->asInt();

        $map->property(TestValueObject::FLOAT)->to('float')->asDecimal(16, 8);

        $map->property(TestValueObject::BOOL)->to('bool')->asBool();
    }
}
