<?php

namespace Dms\Cli\Expressive\Migrations;

use Illuminate\Config\Repository;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends Command
{
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    protected $config;

    /**
     * Create a new migration rollback command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @return void
     */
    public function __construct(Migrator $migrator, Repository $config)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->config = $config;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:rollback')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.', 'default')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Force the operation to run when in production.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.')
            ->addOption('pretend', null, InputOption::VALUE_OPTIONAL, 'Dump the SQL queries that would be run.')
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted.')
            ->setDescription('Rollback the last database migration')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setConnection($input->getOption('database'));

        $this->migrator->rollback(
            $this->getMigrationPaths(),
            [
                'pretend' => $input->getOption('pretend'),
                'step' => (int) $input->getOption('step'),
            ]
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
        $path = $this->config->get('dms.database.migrations.dir', null) ?? database_path('migrations/');

        return [$path];
    }
}
