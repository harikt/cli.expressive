<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Cms\Modules\Fields;

use Dms\Core\Common\Crud\Definition\Form\ValueObjectFieldDefinition;
use Dms\Core\Common\Crud\Form\ValueObjectField;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestGeoValueObject;
use Dms\Common\Structure\Field;

/**
 * The Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestGeoValueObject value object field.
 */
class TestGeoValueObjectField extends ValueObjectField
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
        $form->bindTo(TestGeoValueObject::class);

        $form->section('Details', [
            $form->field(
                Field::create('lat_lng', 'Lat Lng')->arrayOf(
                    Field::element()->latLng()->required()
                )
            )->bindToProperty(TestGeoValueObject::LAT_LNG),
            //
            $form->field(
                Field::create('street_address', 'Street Address')->arrayOf(
                    Field::element()->streetAddress()->required()
                )
            )->bindToProperty(TestGeoValueObject::STREET_ADDRESS),
            //
            $form->field(
                Field::create('street_address_with_lat_lng', 'Street Address With Lat Lng')->arrayOf(
                    Field::element()->streetAddressWithLatLng()->required()
                )
            )->bindToProperty(TestGeoValueObject::STREET_ADDRESS_WITH_LAT_LNG),
            //
        ]);
    }
}
