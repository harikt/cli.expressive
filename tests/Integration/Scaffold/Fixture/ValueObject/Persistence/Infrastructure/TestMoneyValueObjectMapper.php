<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestMoneyValueObject;
use Dms\Common\Structure\Money\Persistence\MoneyMapper;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestMoneyValueObject value object mapper.
 */
class TestMoneyValueObjectMapper extends IndependentValueObjectMapper
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
        $map->type(TestMoneyValueObject::class);

        $map->enum(TestMoneyValueObject::CURRENCY)->to('currency')->asVarchar(3);

        $map->enum(TestMoneyValueObject::NULLABLE_CURRENCY)->to('nullable_currency')->nullable()->asVarchar(3);

        $map->embedded(TestMoneyValueObject::MONEY)
            ->using(new MoneyMapper('money_amount', 'money_currency'));

        $map->embedded(TestMoneyValueObject::NULLABLE_MONEY)
            ->withIssetColumn('nullable_money_amount')
            ->using(new MoneyMapper('nullable_money_amount', 'nullable_money_currency'));
    }
}
