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

class NodeImportCommand extends Command
{
    public function configure()
    {
        $this->setName('node:import')
            ->setDescription('Import nodes from a package')
            ->addArgument(
                'fqpn',
                InputArgument::REQUIRED,
                'Fully Qualified Package Name (FQNN)'
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
        $fqpn = $input->getArgument('fqpn');
        $path = $input->getOption('path');
        if ($path) {
            chdir($path);
        }
        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $loader = new GraphLoader();
        $graph = $loader->load($workingDir . '/package.yaml');
        $package = $graph->getPackage($fqpn);
        foreach ($package->getNodes() as $node) {
            $output->writeLn($node->getFqnn());
            echo $node->toYaml();
            $graph->persist($node->getFqnn(), $node->toYaml());
        }
    }
}
