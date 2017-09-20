<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Migrations;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * The repository instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
     * @return void
     */
    public function __construct(MigrationRepositoryInterface $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setName('migrate:install')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.')
            ->setDescription('Create the migration repository')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->repository->repositoryExists()) {
            $output->writeln('<info>Migration table already exists.</info>');

            return;
        }

        $this->repository->setSource($input->getOption('database'));
        $this->repository->createRepository();
        $output->writeln('<info>Migration table created successfully.</info>');
    }
}
