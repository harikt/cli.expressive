<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Scaffold\CodeGeneration;

use Dms\Cli\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Cli\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Cli\Expressive\Scaffold\ScaffoldPersistenceContext;
use Dms\Common\Structure\Money\Money;
use Dms\Common\Structure\Money\Persistence\MoneyMapper;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class MoneyPropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [Money::class];
    }

    protected function doGeneratePersistenceMappingObjectMapperCode(
        ScaffoldPersistenceContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass,
        string $columnName
    ) {
        if (!$isCollection && $property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '_amount\')');
        }

        $code->addNamespaceImport(MoneyMapper::class);
        $code->getCode()->append('->using(new MoneyMapper(\'' . $columnName . '_amount\', \'' . $columnName . '_currency\'))');
    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        $code->getCode()->append('->money()');
    }
}
