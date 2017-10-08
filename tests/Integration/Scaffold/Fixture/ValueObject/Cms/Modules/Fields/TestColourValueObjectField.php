<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Cms\Modules\Fields;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestColourValueObject;
use Dms\Common\Structure\Field;
use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestColourValueObject value object field.
 */
class TestColourValueObjectField extends ValueObjectField
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
        $form->bindTo(TestColourValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('colour', 'Colour')->colour()->required()
            )->bindToProperty(TestColourValueObject::COLOUR),
            //
            $form->field(
                Field::create('nullable_colour', 'Nullable Colour')->colour()
            )->bindToProperty(TestColourValueObject::NULLABLE_COLOUR),
            //
            $form->field(
                Field::create('transparent_colour', 'Transparent Colour')->colourWithTransparency()->required()
            )->bindToProperty(TestColourValueObject::TRANSPARENT_COLOUR),
            //
            $form->field(
                Field::create('nullable_transparent_colour', 'Nullable Transparent Colour')->colourWithTransparency()
            )->bindToProperty(TestColourValueObject::NULLABLE_TRANSPARENT_COLOUR),
            //
        ]);
    }
}
