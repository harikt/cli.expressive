<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Cms\Modules;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestEntity;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Domain\TestRelatedEntity;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Services\ITestEntityRepository;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ToManyRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Common\Structure\Field;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;

/**
 * The test-related-entity module.
 */
class TestRelatedEntityModule extends CrudModule
{
    /**
     * @var ITestEntityRepository
     */
    protected $testEntityRepository;


    public function __construct(ITestRelatedEntityRepository $dataSource, IAuthSystem $authSystem, ITestEntityRepository $testEntityRepository)
    {
        $this->testEntityRepository = $testEntityRepository;
        parent::__construct($dataSource, $authSystem);
    }

    /**
     * Defines the structure of this module.
     *
     * @param CrudModuleDefinition $module
     */
    protected function defineCrudModule(CrudModuleDefinition $module)
    {
        $module->name('test-related-entity');

        $module->labelObjects()->fromProperty(/* FIXME: */ TestRelatedEntity::ID);

        $module->metadata([
            'icon' => ''
        ]);

        $module->crudForm(function (CrudFormDefinition $form) {
            $form->section('Details', [
                $form->field(
                    Field::create('parent', 'Parent')
                        ->entityFrom($this->testEntityRepository)
                        ->required()
                        ->labelledBy(/* FIXME: */ TestEntity::ID)
                )->bindToProperty(TestRelatedEntity::PARENT),
                //
            ]);
        });

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(function (SummaryTableDefinition $table) {
            $table->mapProperty(TestRelatedEntity::PARENT)->to(Field::create('parent', 'Parent')
                ->entityFrom($this->testEntityRepository)
                ->required()
                ->labelledBy(/* FIXME: */ TestEntity::ID));


            $table->view('all', 'All')
                ->loadAll()
                ->asDefault();
        });
    }
}
