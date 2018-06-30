<?php

namespace Networq\Cli\Command;

use RuntimeException;
use Networq\Cli\AutoGraphLoader;
use Networq\Loader\PackageLoader;
use Networq\Model\Graph;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class LinkCommand extends Command
{
    public function configure()
    {
        $this->setName('link')
            ->setDescription('Link current package directory in ~/.nqp')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $nqpPath = $_SERVER['HOME'] . '/.nqp';
        if (!file_exists($nqpPath)) {
            mkdir($nqpPath);
        }
        $output->writeLn("nqp path: " . $nqpPath);

        $graph = new Graph('test');

        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $filename = $workingDir . '/package.yaml';

        $loader = new PackageLoader();
        if (!file_exists($filename)) {
            throw new RuntimeException('File not found: ' . $filename);
        }
        $package = $loader->load($graph, $filename);
        print_r($package);

        $ownerPath = $nqpPath . '/' . $package->getOwnerName();
        if (!file_exists($ownerPath)) {
            mkdir($ownerPath);
        }

        $linkPath = $nqpPath . '/' . $package->getFqpnDir();
        if (file_exists($linkPath)) {
            throw new RuntimeException($linkPath . ' already exists. Remove it before create a new link.');
        }

        symlink($workingDir, $linkPath);

        exit("DONE" . PHP_EOL);
    }
}
