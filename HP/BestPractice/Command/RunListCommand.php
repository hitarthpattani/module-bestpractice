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

class RunListCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run:list';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('lists all tests to run')
        ->setName(self::$defaultName)
        ->setHelp('This command allows you to run all tests in the suite');
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

        foreach ($runner->getConfig() as $test => $config) {
            $output->writeln(strtolower($test));
        }

        $output->writeln($runner->output);
    }
}
