<?php

namespace Dms\Web\Expressive\Migrations;

use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCommand extends Command
{

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration rollback command instance.
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
            ->setName('migrate:reset')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) of migrations files to be executed.')
            ->addOption('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.')
            ->setDescription('Rollback all database migrations')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setConnection($input->getOption('database'));

        // First, we'll make sure that the migration table actually exists before we
        // start trying to rollback and re-run all of the migrations. If it's not
        // present we'll just bail out with an info message for the developers.
        if (! $this->migrator->repositoryExists()) {
            return $output->writeln('<comment>Migration table not found.</comment>');
        }

        $this->migrator->reset(
            $this->getMigrationPaths(), $input->getOption('pretend')
        );

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $output->writeln($note);
        }
    }

    protected function getMigrationPaths()
    {
        $path = config('dms.database.migrations.dir') ?? database_path('migrations/');

        return [$path];
    }
}
