<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Install;

use Dms\Core\Exception\InvalidOperationException;
use Dms\Core\ICms;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
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

    /**
     * DmsInstallCommand constructor.
     *
     * @param Composer $composer
     */
    public function __construct(Composer $composer, Filesystem $filesystem)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->filesystem = $filesystem;
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
        $output->writeln("<info>Not completed!</info>");
        return;

        if (!\DB::connection()->getDatabaseName()) {
            throw InvalidOperationException::format('Cannot install: database is required, please verify config');
        }

        $this->disableMySqlStrictMode($output);

        if (!$this->ensureMySqlInnoDbLargePrefixIsEnabled($output)) {
            return;
        }

        $this->cleanDefaultModelsAndEntities($output);

        $this->scaffoldAppCms($output);

        $this->scaffoldAppOrm($output);

        $this->scaffoldDatabaseSeeders($output);

        $this->scaffoldAppServiceProvider($output);

        $this->publishAssets();

        $this->setUpInitialDatabase($output);

        $this->addPathsToGitIgnore($output);

        $this->addDmsUpdateCommandToComposerJsonHook($output);

        $this->createDefaultDirectories($output);

        $output->writeln('<info>Done! Good luck with your project.</info>');
    }

    protected function ensureMySqlInnoDbLargePrefixIsEnabled($output) : bool
    {
        if (env('DB_CONNECTION') !== 'mysql') {
            return true;
        }

        $hasLargePrefixEnabled = \DB::select('SELECT @@innodb_large_prefix AS flag')[0]->flag;

        if (!$hasLargePrefixEnabled) {
            $output->writeln('<warn>Please enable innodb_large_prefix. See here https://dev.mysql.com/doc/refman/5.7/en/innodb-parameters.html#sysvar_innodb_large_prefix</warn>');
            return false;
        }

        return true;
    }

    protected function cleanDefaultModelsAndEntities($output)
    {
        $this->filesystem->delete(app_path('User.php'));
        $output->writlen('<info>Deleted: ' . app_path('User.php') . '</info>');
        $this->filesystem->cleanDirectory(database_path('migrations/'));
        $output->writeln('<info>Deleted: ' . database_path('migrations/') . '*</info>');
    }

    protected function disableMySqlStrictMode($output)
    {
        $this->filesystem->put(config_path('database.php'), preg_replace('/([\'"]strict[\'"]\s*=>\s*)true/', '$1false', file_get_contents(config_path('database.php'))));
        app('config')->set('database.mysql.strict', false);
        $output->writeln('<info>Disabled MySQL strict mode</info>');
    }

    protected function scaffoldAppCms($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/AppCms.php.stub', app_path('AppCms.php'));
        require_once app_path('AppCms.php');
        app()->singleton(ICms::class, \App\AppCms::class);
        $output->writeln('<info>Created: ' . app_path('AppCms.php') . '</info>');
    }

    protected function scaffoldAppOrm($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/AppOrm.php.stub', app_path('AppOrm.php'));
        require_once app_path('AppOrm.php');
        app()->singleton(IOrm::class, \App\AppOrm::class);
        $output->writeln('<info>Created: ' . app_path('AppOrm.php') . '</info>');
    }

    protected function scaffoldDatabaseSeeders($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/DmsAdminSeeder.php.stub', database_path('seeds/DmsAdminSeeder.php'));
        require_once database_path('seeds/DmsAdminSeeder.php');
        $output->writeln('<info>Created: ' . database_path('seeds/DmsAdminSeeder.php') . '</info>');
        $this->filesystem->copy(__DIR__ . '/Stubs/DatabaseSeeder.php.stub', database_path('seeds/DatabaseSeeder.php'));
        require_once database_path('seeds/DatabaseSeeder.php');
        $output->writeln('<info>Updated: ' . database_path('seeds/DatabaseSeeder.php') . '</info>');
    }

    protected function scaffoldAppServiceProvider($output)
    {
        $this->filesystem->copy(__DIR__ . '/Stubs/AppServiceProvider.php.stub', app_path('Providers/AppServiceProvider.php'));
        $output->writeln('<info>Updated: ' . app_path('Providers/AppServiceProvider.php') . '</info>');
    }

    protected function publishAssets()
    {
        app('config')->set(['dms' => require __DIR__ . '/../../config/dms.php']);
    }

    protected function setUpInitialDatabase($output)
    {
        $command = 'dms:make:migration';

        $arguments = [
            '--name' => 'initial_db',
            'command' => $command,
        ];

        $this->getApplication()->find($command)->run(
            new ArrayInput($arguments), $output
        );

        $output->writeln('<info>Executed: php artisan dms:make:migration initial_db</info>');

        $this->getApplication()->find('migrate')->run(
            new ArrayInput([
                'command' => 'migrate',
            ]), $output
        );
        $output->writeln('<info>Executed: php artisan migrate</info>');

        $this->getApplication()->find('db:seed')->run(
            new ArrayInput([
                'command' => 'db:seed',
            ]), $output
        );
        $output->writeln('<info>Executed: php artisan db:seed</info>');
    }

    protected function addPathsToGitIgnore($output)
    {
        file_put_contents(
            app_path('../.gitignore'),
            '/storage/dms/' . PHP_EOL
            . '/public/files/' . PHP_EOL,
            FILE_APPEND
        );
        $output->writeln('<info>Added to .gitignore</info>');
    }

    protected function addDmsUpdateCommandToComposerJsonHook($output)
    {
        $composerJsonData                                 = json_decode(file_get_contents(base_path('composer.json')), true);
        $composerJsonData['scripts']['post-update-cmd'][] = 'php artisan dms:update';
        file_put_contents(base_path('composer.json'), json_encode($composerJsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $output->writeln('<info>Added php artisan dms:update to post-update hook in composer.json</info>');
    }

    protected function createDefaultDirectories()
    {
        @mkdir(app_path('Domain/Entities'));
        $output->writeln('<info>Created the app/Domain/Entities directory</info>');
        @mkdir(app_path('Domain/Services'));
        $output->writeln('<info>Created the app/Domain/Services directory</info>');
    }
}
