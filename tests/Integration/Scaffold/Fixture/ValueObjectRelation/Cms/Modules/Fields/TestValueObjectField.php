<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Cms\Modules\Fields;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestValueObject;
use Dms\Common\Structure\Field;
use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestValueObject value object field.
 */
class TestValueObjectField extends ValueObjectField
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
        $form->bindTo(TestValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('string', 'String')->string()->required()
            )->bindToProperty(TestValueObject::STRING),
            //
        ]);
    }
}
