<?php

namespace Dms\Cli\Expressive\Migrations;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
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
     * @param  \Illuminate\Database\Migrations\Migrator $migrator
     * @return \Illuminate\Database\Console\Migrations\StatusCommand
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:status')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to use.')
            ->setDescription('Show the status of each migration')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrator->setConnection($input->getOption('database'));

        if (! $this->migrator->repositoryExists()) {
            return $output->writeln('<error>No migrations found.</error>');
        }

        $ran = $this->migrator->getRepository()->getRan();

        if (count($migrations = $this->getStatusFor($ran)) > 0) {
            $rows = $migrations->toArray();
            $headers = ['Ran?', 'Migration'];
            $table = new Table($output);
            $table->setHeaders((array) $headers)->setRows($rows)->setStyle('default')->render();
        } else {
            $output->writeln('<error>No migrations found</error>');
        }
    }

    /**
     * Get the status for the given ran migrations.
     *
     * @param  array  $ran
     * @return \Illuminate\Support\Collection
     */
    protected function getStatusFor(array $ran)
    {
        return Collection::make($this->getAllMigrationFiles())
                    ->map(function ($migration) use ($ran) {
                        $migrationName = $this->migrator->getMigrationName($migration);

                        return in_array($migrationName, $ran)
                                ? ['<info>Y</info>', $migrationName]
                                : ['<fg=red>N</fg=red>', $migrationName];
                    });
    }

    /**
     * Get an array of all of the migration files.
     *
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        return $this->migrator->getMigrationFiles($this->getMigrationPaths());
    }

    protected function getMigrationPaths()
    {
        $path = config('dms.database.migrations.dir') ?? database_path('migrations/');

        return [$path];
    }
}
