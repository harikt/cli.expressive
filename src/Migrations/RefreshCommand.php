<?php

namespace Dms\Cli\Expressive\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migrate:refresh')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.', 'default')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Force the operation to run when in production.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.')
            ->addOption('seed', null, InputOption::VALUE_OPTIONAL, 'Indicates if the seed task should be re-run.')
            ->addOption('seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.')
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted & re-run.')
            ->setDescription('Reset and re-run all migrations')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Next we'll gather some of the options so that we can have the right options
        // to pass to the commands. This includes options such as which database to
        // use and the path to use for the migration. Then we'll run the command.
        $database = $input->getOption('database');

        $path = $input->getOption('path');

        $force = $input->getOption('force');

        // If the "step" option is specified it means we only want to rollback a small
        // number of migrations before migrating again. For example, the user might
        // only rollback and remigrate the latest four migrations instead of all.
        $step = $input->getOption('step') ?: 0;

        if ($step > 0) {
            $this->runRollback($database, $path, $step, $force, $output);
        } else {
            $this->runReset($database, $path, $force, $output);
        }

        // The refresh command is essentially just a brief aggregate of a few other of
        // the migration commands and just provides a convenient wrapper to execute
        // them in succession. We'll also see if we need to re-seed the database.
        $this->call('migrate', [
            '--database' => $database,
            '--path' => $path,
            '--force' => $force,
        ], $output);

        if ($this->needsSeeding($input)) {
            $this->runSeeder($database, $input, $output);
        }
    }

    /**
     * Run the rollback command.
     *
     * @param  string  $database
     * @param  string  $path
     * @param  bool  $step
     * @param  bool  $force
     * @return void
     */
    protected function runRollback($database, $path, $step, $force, $output)
    {
        $this->call('migrate:rollback', [
            '--database' => $database,
            '--path' => $path,
            '--step' => $step,
            '--force' => $force,
        ], $output);
    }

    /**
     * Run the reset command.
     *
     * @param  string  $database
     * @param  string  $path
     * @param  bool  $force
     * @return void
     */
    protected function runReset($database, $path, $force, $output)
    {
        $this->call('migrate:reset', [
            '--database' => $database,
            '--path' => $path,
            '--force' => $force,
        ], $output);
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
    protected function runSeeder($database, InputInterface $input, OutputInterface $output)
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
            new ArrayInput($arguments),
            $output
        );
    }
}
