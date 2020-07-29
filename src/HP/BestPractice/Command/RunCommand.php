<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice\Command;

use HP\BestPractice\Command\BranchCommand;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends BranchCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run:test';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('runs a single tests')
            ->setName(self::$defaultName)
            ->setHelp('This command allows you to run a single test')
            ->addArgument('name', InputArgument::REQUIRED, 'which test to run?');
        parent::configure();
    }
}
