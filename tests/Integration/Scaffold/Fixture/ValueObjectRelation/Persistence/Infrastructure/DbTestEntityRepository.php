<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Persistence\Services\ITestEntityRepository;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestEntity;

/**
 * The database repository implementation for the Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectRelation\Domain\TestEntity entity.
 */
class DbTestEntityRepository extends DbRepository implements ITestEntityRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TestEntity::class));
    }
}
