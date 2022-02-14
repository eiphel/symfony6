<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:clean-tmp',
    description: 'Delete old files left in the tmp directory',
)]
class CleanTmpCommand extends Command
{
    private $parameter;

    public function __construct(ParameterBagInterface $parameter)
    {
        $this->parameter = $parameter;
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tmpDir = $this->parameter->get('tmp_directory');

        $i = 0;

        foreach(scandir($tmpDir) as $fichier) {
            if(is_dir($fichier) || $fichier == '.' || $fichier == '..') {
                continue;
            }

            $pathFile = $tmpDir . '/' . $fichier;

            if ((time() - filemtime($pathFile)) >= 86400) { // 1 day

                unlink($pathFile);
                $output->writeln($fichier);
                $i++;
            }
        }

        $io->success($i . ' file(s) deleted');

        return Command::SUCCESS;
    }
}
