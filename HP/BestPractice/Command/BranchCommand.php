<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BranchCommand extends Command
{
    /**
     * Apply a branch non-required argument to all classes that will use this.
     * Parent::configure() is required to use.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('branch', InputArgument::OPTIONAL, 'Which branch or commit hash to compare?');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int exit code
     * @SuppressWarnings("unused")
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new \HP\BestPractice\TestRunner();

        $arguments = $input->getArguments();
        $commandToRun = $arguments['name'] ?? null;
        $branchToCompare = $input->getArgument('branch');

        $paths = $this->defineMagentoSpecsTestPaths($runner);
        $fileList = $this->getChangedFiles($branchToCompare, $paths);

        foreach ($paths as $test => $config) {
            if ($commandToRun === null || strtolower($test) == $commandToRun) {
                if ($fileList) {
                    $runner->setFiles($fileList);
                }
                if ($paths) {
                    $runner->setPaths($config['paths']);
                }
                $runner->run("\HP\BestPractice\Task\/".$test, $config['config']);
            }
        }
        $runner->summary();
        $output->writeln($runner->output);

        return $runner->exitCode();
    }

    /**
     * @param \HP\BestPractice\TestRunner $runner
     *
     * @return bool
     */
    protected function defineMagentoSpecsTestPaths($runner)
    {
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        $testPathFinder = new \HP\BestPractice\TestPathFinder();

        $paths = [];

        if ($testPathFinder->isCustomModulePath($filesystem)) {
            $paths = $testPathFinder->getModuleTestPaths($runner->getConfig(), $filesystem, true);
        } elseif ($testPathFinder->isMagentoInstallationPath($filesystem)) {
            $paths = $testPathFinder->getMagentoTestPaths(
                $runner->getConfig(),
                $runner->getMagentoSpecsConfig(),
                $filesystem
            );
        } else {
            $paths = $testPathFinder->getModuleTestPaths($runner->getConfig(), $filesystem);
        }

        return $paths;
    }

    /**
     * @param string $branchToCompare
     * @param array $paths
     *
     * @return array|null
     */
    protected function getChangedFiles($branchToCompare, $paths)
    {
        if ($branchToCompare === null) {
            return null;
        }
        $process = new Process(['git', 'diff', '--name-only', '--diff-filter=MUXAR', $branchToCompare]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $filesList = $process->getOutput();
        $filesList = explode("\n", trim($filesList));

        $filterPaths = [];
        foreach ($paths as $config) {
            foreach ($config['paths'] as $path) {
                $filterPaths[] = $path;
            }
        }
        $filterPaths = array_unique($filterPaths);

        foreach ($filterPaths as &$pathToCompare) {
            $pathToCompare = str_replace(APP_PATH, '', $pathToCompare);
        }

        $result = array_filter(
            $filesList,
            function ($file) use ($filterPaths) {
                foreach ($filterPaths as $pathToCompare) {
                    if (false !== strpos($file, $pathToCompare)) {
                        return true;
                    }
                }
                return false;
            }
        );

        $result = array_map(
            function ($file) {
                return APP_PATH . $file;
            },
            $result
        );

        return $result;
    }
}
