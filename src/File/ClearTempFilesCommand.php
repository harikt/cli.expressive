<?php declare(strict_types=1);

namespace Dms\Cli\Expressive\File;

use Dms\Core\Util\IClock;
use Dms\Web\Expressive\File\Persistence\ITemporaryFileRepository;
use Dms\Web\Expressive\File\TemporaryFile;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The clear temp file command.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ClearTempFilesCommand extends Command
{

    /**
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * @var ITemporaryFileRepository
     */
    protected $tempFileRepo;

    /**
     * @var IClock
     */
    protected $clock;

    /**
     * ClearTempFilesCommand constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, ITemporaryFileRepository $tempFileRepo, IClock $clock)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->clock = $clock;
        $this->tempFileRepo = $tempFileRepo;
    }


    protected function configure()
    {
        $this
            ->setName('dms:clear-temp-files')
            ->setDescription('Clears the expired temporary files')
            ;
    }

    /**
     * Execute the console command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expiredFiles = $this->tempFileRepo->matching(
            $this->tempFileRepo->criteria()
                ->whereSatisfies(TemporaryFile::expiredSpec($this->clock))
        );

        foreach ($expiredFiles as $file) {
            if ($file->getFile()->exists()) {
                $this->filesystem->delete($file->getFile()->getFullPath());
                $output->writeln("<info>Deleted {$file->getFile()->getFullPath()}</info>");
            }
        }

        $this->tempFileRepo->removeAll($expiredFiles);
    }
}
