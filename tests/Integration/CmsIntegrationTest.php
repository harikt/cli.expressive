<?php

namespace Dms\Cli\Expressive\Tests\Integration;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\ICms;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\LocalAdmin;
use Dms\Web\Expressive\Auth\Password\HashedPassword;
use Dms\Web\Expressive\DmsServiceProvider;
use Dms\Cli\Expressive\Tests\Integration\Fixtures\DmsFixture;
use Illuminate\Routing\RouteCollection;
use Laravel\BrowserKitTesting\Concerns\InteractsWithSession;
use Laravel\BrowserKitTesting\Concerns\MakesHttpRequests;
use PHPUnit\Framework\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class CmsIntegrationTest extends TestCase
{
    use MakesHttpRequests, InteractsWithSession;

    /**
     * @var DmsFixture
     */
    protected static $fixture;

    /**
     * @var bool
     */
    protected static $isSetUp = false;

    /**
     * @return DmsFixture
     * @throws \Exception
     */
    protected static function getFixture()
    {
        throw new \Exception('Please implement the ' . get_called_class() . '::' . __FUNCTION__ . ' method');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$fixture = static::getFixture();
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $app['config']->set('dms', require __DIR__ . '/../../../config/dms.php');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->singleton(ICms::class, static::$fixture->getCmsClass());
        $app->singleton(IOrm::class, static::$fixture->getOrmClass());

        if (!static::$isSetUp) {
            static::$fixture->setUpBeforeClass($app);
            static::$isSetUp = true;
        }

        static::$fixture->setUp($app);
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', \Illuminate\Foundation\Exceptions\Handler::class);
    }

    protected function actingAsUser(Admin $user = null)
    {
        $this->actingAs($user ?: $this->getMockUser(), 'dms');
    }

    /**
     * @return Admin
     */
    protected function getMockUser()
    {
        return new LocalAdmin(
            'Test Admin',
            new EmailAddress('test@test.com'),
            'admin',
            new HashedPassword('some-hash', 'algo', 10)
        );
    }
}
