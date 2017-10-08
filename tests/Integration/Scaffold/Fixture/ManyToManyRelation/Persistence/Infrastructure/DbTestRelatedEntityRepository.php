<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Infrastructure;

use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;
use Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;

/**
 * The database repository implementation for the Dms\Cli\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity entity.
 */
class DbTestRelatedEntityRepository extends DbRepository implements ITestRelatedEntityRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TestRelatedEntity::class));
    }
}
