<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\Migrations;

use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @return void
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    protected function configure()
    {
        $this
            ->setName('migrate')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.', 'default')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Force the operation to run when in production.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.')
            ->addOption('pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.')
            ->addOption('seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.')
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'Force the migrations to be run so they can be rolled back individually.')
            ->setDescription('Run the database migrations')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareDatabase($input, $output);

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        $path = config('dms.database.migrations.dir') ?? database_path('migrations/');
        $this->migrator->run([$path], [
            'pretend' => $input->getOption('pretend'),
            'step' => $input->getOption('step'),
        ]);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $output->writeln($note);
        }

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
        if ($input->getOption('seed')) {
            $command = 'db:seed';

            $arguments = [
                '--force' => true,
                'command' => $command,
            ];

            return $this->getApplication()->find($command)->run(
                new ArrayInput($arguments), $output
            );
        }
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setConnection($input->getOption('database'));

        if (! $this->migrator->repositoryExists()) {
            $command = 'migrate:install';

            $arguments = [
                '--database' => $input->getOption('database'),
                'command' => $command,
            ];

            return $this->getApplication()->find($command)->run(
                new ArrayInput($arguments), $output
            );
        }
    }
}
