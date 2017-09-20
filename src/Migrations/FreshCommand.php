<?php

namespace Dms\Web\Expressive\Migrations;

use Illuminate\Database\ConnectionResolverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FreshCommand extends Command
{

    /**
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    public function __construct(ConnectionResolverInterface $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:fresh')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.', 'default')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Force the operation to run when in production.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.')
            ->addOption('seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.')
            ->addOption('seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.')
            ->setDescription('Drop all tables and re-run all migrations')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dropAllTables(
            $database = $input->getOption('database')
        );

        $output->writeln('<info>Dropped all tables successfully.</info>');

        $this->call('migrate', [
            '--database' => $database,
            '--path' => $input->getOption('path'),
            '--force' => true,
        ], $output);

        if ($this->needsSeeding($input)) {
            $this->runSeeder($database);
        }
    }

    /**
     * Drop all of the database tables.
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllTables($database)
    {
        $this->resolver->connection($database)
                    ->getSchemaBuilder()
                    ->dropAllTables();
    }

    /**
     * Determine if the developer has requested database seeding.
     *
     * @return bool
     */
    protected function needsSeeding($input)
    {
        return $input->getOption('seed') || $input->getOption('seeder');
    }

    /**
     * Run the database seeder command.
     *
     * @param  string  $database
     * @return void
     */
    protected function runSeeder($database)
    {
        $this->call('db:seed', [
            '--database' => $database,
            '--class' => $input->getOption('seeder') ?: 'DatabaseSeeder',
            '--force' => $input->getOption('force'),
        ], $output);
    }

    protected function call($command, $arguments, $output)
    {
        $args['command'] = $command;
        return $this->getApplication()->find($command)->run(
            new ArrayInput($arguments), $output
        );
    }
}
