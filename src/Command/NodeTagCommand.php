<?php

namespace Networq\Cli\Command;

use RuntimeException;
use Networq\Loader\GraphLoader;
use Networq\Model\Graph;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NodeTagCommand extends Command
{
    public function configure()
    {
        $this->setName('node:tag')
            ->setDescription('Tag a node')
            ->addArgument(
                'fqnn',
                InputArgument::REQUIRED,
                'Fully Qualified Node Name (FQNN)'
            )
            ->addArgument(
                'fqtn',
                InputArgument::REQUIRED,
                'Fully Qualified Type Name (FQTN)'
            )
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Working directory'
            );
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fqnn = $input->getArgument('fqnn');
        $fqtn = $input->getArgument('fqtn');
        $path = $input->getOption('path');
        if ($path) {
            chdir($path);
        }
        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $loader = new GraphLoader();
        $graph = $loader->load($workingDir . '/package.yaml');
        $node = $graph->getNode($fqnn);
        $graph->tagNode($node, $fqtn);
    }
}
