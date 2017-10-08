<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Domain\TestEntity;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Persistence\Services\ITestEntityRepository;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;

/**
 * The database repository implementation for the Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\Simple\Domain\TestEntity entity.
 */
class DbTestEntityRepository extends DbRepository implements ITestEntityRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TestEntity::class));
    }
}
