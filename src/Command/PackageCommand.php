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

class PackageCommand extends Command
{
    public function configure()
    {
        $this->setName('package')
            ->setDescription('Show package information')
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

        $graph = new Graph('test');
        $loader = new PackageLoader();
        $package = $loader->load($graph, $workingDir . '/package.yaml');

        $output->writeLn("FQPN: <info>" . $package->getFqpn() . '</info>');
        $output->writeLn("Description: <info>" . $package->getDescription() . '</info>');
        $output->writeLn("License: <info>" . $package->getLicense() . '</info>');
        $output->writeLn("Dependencies: ");
        foreach ($package->getDependencies() as $dependency) {
            $output->writeLn(' - <info>' . $dependency->getName() . '</info> ' . $dependency->getVersion());
        }

        exit("DONE" . PHP_EOL);
    }
}
