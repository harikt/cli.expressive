<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestFileValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestFileValueObject value object field.
 */
class TestFileValueObjectField extends ValueObjectField
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
        $form->bindTo(TestFileValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('file', 'File')->arrayOf(
                    Field::element()
                        ->file()
                        ->required()
                        ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
                )
            )->bindToProperty(TestFileValueObject::FILE),
            //
            $form->field(
                Field::create('image', 'Image')->arrayOf(
                    Field::element()
                        ->file()
                        ->required()
                        ->moveToPathWithRandomFileName(public_path('app/test_file_value_object'))
                )
            )->bindToProperty(TestFileValueObject::IMAGE),
            //
        ]);
    }
}
