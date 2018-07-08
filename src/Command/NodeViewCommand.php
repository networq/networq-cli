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

class NodeViewCommand extends Command
{
    public function configure()
    {
        $this->setName('node:view')
            ->setDescription('View a node')
            ->addArgument(
                'fqnn',
                InputArgument::REQUIRED,
                'Fully Qualified Node Name (FQNN)'
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
        $path = $input->getOption('path');
        if ($path) {
            chdir($path);
        }
        $workingDir = getcwd();
        $output->writeLn("Working directory: " . $workingDir);

        $loader = new GraphLoader();
        $graph = $loader->load($workingDir . '/package.yaml');
        $node = $graph->getNode($fqnn);
        foreach ($node->getTags() as $tag) {
            $output->writeLn('<info>' . $tag->getType()->getFqtn() . '</info>');
            foreach ($tag->getProperties() as $p) {
                $value = $p->getValueString();
                $output->writeLn(' - <info>' . $p->getField()->getName() . '</info> <comment>' . $value . '</comment>');
            }
        }
    }
}
