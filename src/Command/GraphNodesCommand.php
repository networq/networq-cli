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

class GraphNodesCommand extends Command
{
    public function configure()
    {
        $this->setName('graph:nodes')
            ->setDescription('List nodes')
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
        $path = $input->getOption('path');
        if ($path) {
            chdir($path);
        }
        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $loader = new GraphLoader();
        $graph = $loader->load($workingDir . '/package.yaml');
        foreach ($graph->getPackages() as $package) {
            $output->writeLn("Package: <info>" . $package->getFqpn() . '</info>');
            foreach ($package->getNodes() as $node) {
                $output->writeLn("   <comment>" . $node->getFqnn() . '</comment>');
            }
        }

        exit("DONE" . PHP_EOL);
    }
}
