<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Install;

use Dms\Core\Exception\InvalidOperationException;
use Dms\Core\ICms;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Ioc\IIocContainer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The dms:install command
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsInstallCommand extends Command
{
    /**
     * @var Composer
     */
    protected $composer;

    protected $input;

    protected $filesystem;

    protected $connection;

    protected $container;

    /**
     * DmsInstallCommand constructor.
     *
     * @param Composer $composer
     */
    public function __construct(
        Composer $composer,
        Filesystem $filesystem,
        IConnection $connection,
        IIocContainer $container
    ) {
        parent::__construct();

        $this->composer = $composer;
        $this->filesystem = $filesystem;
        $this->connection = $connection;
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('dms:install')
            ->setDescription('Installs the dms in the current fresh zend expressive project')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // if (!\DB::connection()->getDatabaseName()) {
        //     throw InvalidOperationException::format('Cannot install: database is required, please verify config');
        // }

        // $this->disableMySqlStrictMode($output);

        // if (!$this->ensureMySqlInnoDbLargePrefixIsEnabled($output)) {
        //     return;
        // }

        $this->cleanDefaultModelsAndEntities($output);

        // $this->scaffoldAppCms($output);

        // $this->scaffoldAppOrm($output);

        $this->scaffoldDatabaseSeeders($output);

        $this->publishAssets();

        $this->setUpInitialDatabase($output);

        $this->addPathsToGitIgnore($output);

        $this->addDmsUpdateCommandToComposerJsonHook($output);

        $this->createDefaultDirectories($output);

        $output->writeln('<info>Done! Good luck with your project.</info>');
    }

    protected function ensureMySqlInnoDbLargePrefixIsEnabled($output) : bool
    {
        if (env('driver') !== 'mysql') {
            return true;
        }

        // $hasLargePrefixEnabled = \DB::select('SELECT @@innodb_large_prefix AS flag')[0]->flag;

        // if (!$hasLargePrefixEnabled) {
        //     $output->writeln('<warn>Please enable innodb_large_prefix. See here https://dev.mysql.com/doc/refman/5.7/en/innodb-parameters.html#sysvar_innodb_large_prefix</warn>');
        //     return false;
        // }

        return true;
    }

    protected function disableMySqlStrictMode($output)
    {
        // $this->filesystem->put(config_path('database.php'), preg_replace('/([\'"]strict[\'"]\s*=>\s*)true/', '$1false', file_get_contents(config_path('database.php'))));
        // app('config')->set('database.mysql.strict', false);
        // $output->writeln('<info>Disabled MySQL strict mode</info>');
    }

    protected function cleanDefaultModelsAndEntities($output)
    {
        $this->filesystem->cleanDirectory($this->projectRoot('database/migrations'));
        $output->writeln('<info>Deleted: ' . $this->projectRoot('database/migrations') . '*'. '</info>');
    }

    protected function scaffoldAppCms($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/AppCms.php.stub', $this->appPath('AppCms.php'));
        require_once $this->appPath('AppCms.php');
        $this->container->bind(IIocContainer::SCOPE_SINGLETON, ICms::class, \App\AppCms::class);
        $output->writeln('<info>Created: ' . $this->appPath('AppCms.php') . '</info>');
    }

    protected function scaffoldAppOrm($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/AppOrm.php.stub', $this->appPath('AppOrm.php'));
        require_once $this->appPath('AppOrm.php');
        $this->container->bind(IIocContainer::SCOPE_SINGLETON, IOrm::class, \App\AppOrm::class);
        $output->writeln('<info>Created: ' . $this->appPath('AppOrm.php') . '</info>');
    }

    protected function scaffoldDatabaseSeeders($output)
    {
        // $this->filesystem->copy(__DIR__ . '/Stubs/DmsAdminSeeder.php.stub', $this->projectRoot('database/seeds/DmsAdminSeeder.php'));
        // require_once $this->projectRoot('database/seeds/DmsAdminSeeder.php');
        // $output->writeln('<info>Created: ' . $this->projectRoot('database/seeds/DmsAdminSeeder.php') . '</info>');
        // $this->filesystem->copy(__DIR__ . '/Stubs/DatabaseSeeder.php.stub', $this->projectRoot('database/seeds/DatabaseSeeder.php'));
        // require_once $this->projectRoot('database/seeds/DatabaseSeeder.php');
        // $output->writeln('<info>Updated: ' . $this->projectRoot('database/seeds/DatabaseSeeder.php') . '</info>');
    }

    protected function publishAssets()
    {
        copy($this->projectRoot('vendor/harikt/web.expressive/config/dms.php'), $this->projectRoot('config/autoload/dms.global.php'));
        $this->filesystem->copyDirectory($this->projectRoot('vendor/harikt/web.expressive/dist'), $this->projectRoot('public/vendor/dms'));
    }

    protected function setUpInitialDatabase($output)
    {
        $command = 'migrate:install';

        $arguments = [];

        $this->getApplication()->find($command)->run(
            new ArrayInput($arguments),
            $output
        );

        $output->writeln('<info>Executed: php console migrate:install</info>');

        $command = 'dms:make:migration';

        $arguments = [
            'name' => 'initial_db',
            'command' => $command,
        ];

        $this->getApplication()->find($command)->run(
            new ArrayInput($arguments),
            $output
        );

        $output->writeln('<info>Executed: php console dms:make:migration initial_db</info>');

        $this->getApplication()->find('migrate')->run(
            new ArrayInput([
                'command' => 'migrate',
            ]),
            $output
        );
        $output->writeln('<info>Executed: php console migrate</info>');

        // $this->getApplication()->find('db:seed')->run(
        //     new ArrayInput([
        //         'command' => 'db:seed',
        //     ]),
        //     $output
        // );
        // $output->writeln('<info>Executed: php console db:seed</info>');
    }

    protected function addPathsToGitIgnore($output)
    {
        file_put_contents(
            $this->appPath('../../../.gitignore'),
            '/storage/dms/' . PHP_EOL
            . '/public/files/' . PHP_EOL,
            FILE_APPEND
        );
        $output->writeln('<info>Added to .gitignore</info>');
    }

    protected function addDmsUpdateCommandToComposerJsonHook($output)
    {
        // Do nothing now
        return;
        // $composerJsonData                                 = json_decode(file_get_contents(base_path('composer.json')), true);
        // $composerJsonData['scripts']['post-update-cmd'][] = 'php console dms:update';
        // file_put_contents(base_path('composer.json'), json_encode($composerJsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // $output->writeln('<info>Added php console dms:update to post-update hook in composer.json</info>');
    }

    protected function createDefaultDirectories($output)
    {
        @mkdir($this->appPath('Domain/Entities'), 0755, true);
        $output->writeln('<info>Created the app/Domain/Entities directory</info>');
        @mkdir($this->appPath('Domain/Services'), 0755, true);
        $output->writeln('<info>Created the app/Domain/Services directory</info>');
    }

    protected function appPath($path = null)
    {
        // vendor/harikt/cli.expressive/src/Install
        return dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/src/App/src/' . $path;
    }

    protected function projectRoot($path = null)
    {
        // vendor/harikt/cli.expressive/src/Install
        return dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/' . $path;
    }
}
