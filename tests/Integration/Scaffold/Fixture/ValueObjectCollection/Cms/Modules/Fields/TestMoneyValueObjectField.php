<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestMoneyValueObject;
use Dms\Common\Structure\Field;
use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestMoneyValueObject value object field.
 */
class TestMoneyValueObjectField extends ValueObjectField
{
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
    }

    /**
     * Defines the structure of this value object field.
     *
     * @param ValueObjectFieldDefinition $form
     *
     * @return void
     */
    protected function define(ValueObjectFieldDefinition $form)
    {
        $form->bindTo(TestMoneyValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('money', 'Money')->arrayOf(
                    Field::element()->money()->required()
                )
            )->bindToProperty(TestMoneyValueObject::MONEY),
            //
        ]);
    }
}
