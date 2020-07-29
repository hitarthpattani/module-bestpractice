<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class RunCodeSnifferFixerCommand extends Command
{
    const VALIDATION_NAME = 'CodeSnifferFixer';

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run:phpcbf';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('fix all php code sniffer errors and warnings')
            ->setName(self::$defaultName)
            ->setHelp('Make sure you are adding configuration to .bestpractices.magento.yml.dist to run this command.');
    }

    /**
     * @param mixed $input
     * @param mixed q$output
     * @return null
     * @SuppressWarnings("unused")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new \HP\BestPractice\TestRunner();

        $paths = $this->defineMagentoSpecsTestPaths($runner);

        foreach ($paths as $test => $config) {
            if ($commandToRun === null || strtolower($test) == $commandToRun) {
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

        $runnerConfig = [self::VALIDATION_NAME => ['require-passing' => true]];

        if ($testPathFinder->isCustomModulePath($filesystem)) {
            $paths = $testPathFinder->getModuleTestPaths($runnerConfig, $filesystem, true);
        } elseif ($testPathFinder->isMagentoInstallationPath($filesystem)) {
            $paths = $testPathFinder->getMagentoTestPaths($runnerConfig, $runner->getMagentoSpecsConfig(), $filesystem);
        } else {
            $paths = $testPathFinder->getModuleTestPaths($runnerConfig, $filesystem);
        }

        return $paths;
    }
}
