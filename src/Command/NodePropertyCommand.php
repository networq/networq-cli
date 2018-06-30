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

class NodePropertyCommand extends Command
{
    public function configure()
    {
        $this->setName('node:property')
            ->setDescription('Set a node property on a node')
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
            ->addArgument(
                'field',
                InputArgument::REQUIRED,
                'Field name'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'Value'
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
        $field = $input->getArgument('field');
        $value = $input->getArgument('value');
        $path = $input->getOption('path');
        if ($path) {
            chdir($path);
        }
        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $loader = new GraphLoader();
        $graph = $loader->load($workingDir . '/package.yaml');
        $node = $graph->getNode($fqnn);
        $graph->setNodeProperty($node, $fqtn, $field, $value);
        $output->writeLn("<info>Ok</info>");

    }
}
