<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice\Command;

use HP\BestPractice\Command\BranchCommand;

class RunAllCommand extends BranchCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'run:all';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('runs all tests')
        ->setName(self::$defaultName)
        ->setHelp('This command allows you to run all tests in the suite');
        parent::configure();
    }
}
