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


class InstallCommand extends Command
{
    public function configure()
    {
        $this->setName('install')
            ->setDescription('Install dependencies')
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

        $this->install($graph, $workingDir . '/package.yaml', $output);

        exit("DONE" . PHP_EOL);
    }

    protected $processedPaths = [];

    public function install(Graph $graph, $filename, OutputInterface $output)
    {
        $output->writeLn("<info>Installing dependencies</info> from <comment>$filename</comment>");
        $loader = new PackageLoader();
        if (!file_exists($filename)) {
            throw new RuntimeException('File not found: ' . $filename);
        }
        $package = $loader->load($graph, $filename);

        foreach ($package->getDependencies() as $dependency) {
            $output->writeLn(' - <info>' . $dependency->getName() . '</info> ' . $dependency->getVersion());
            $path = getcwd() . '/packages/' . str_replace(':', '/', $dependency->getName());
            $url = 'https://github.com/' . str_replace(':', '/', $dependency->getName() . '-nqp');
            $output->writeLn('   Path: ' . $path);
            $output->writeLn('   Url: ' . $url);

            if (isset($this->processedPaths[$path])) {
                $output->writeLn('   Already processed during this run');
            } else {

                if (!file_exists($path)) {
                    $output->writeLn('   Creating directory');
                    mkdir($path, 0777, true);
                }

                if (file_exists($path . '/.git')) {
                    $output->writeLn('   <info>Already installed</info>');
                    $cmd = 'git pull origin master';
                    $output->writeLn('   Pulling updates: ' . $cmd);
                    $process = new Process($cmd, $path);
                    $process->run();
                    if (!$process->isSuccessful()) {
                        echo $process->getOutput();
                        echo $process->getErrorOutput();
                        throw new ProcessFailedException($process);
                    }

                } else {
                    $cmd = 'git clone ' . $url . ' ' . $path;
                    $output->writeLn('   Cloning: ' . $cmd);
                    $process = new Process($cmd);
                    $process->run();
                    if (!$process->isSuccessful()) {
                        echo $process->getOutput();
                        echo $process->getErrorOutput();
                        throw new ProcessFailedException($process);
                    }
                }
                $this->install($graph, $path . '/package.yaml', $output);
            }

        }

    }
}
