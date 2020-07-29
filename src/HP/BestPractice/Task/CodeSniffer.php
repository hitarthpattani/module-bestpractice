<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */
/**
 * @phpcs:disable Magento2.Security.InsecureFunction.Found
 * @phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
 */

namespace HP\BestPractice\Task;

class CodeSniffer extends AbstractTask
{
    public const NAME = 'Code Sniffer Validations';
    public const DESC = 'Runs the bundled phpcs against BA paths/files';
    public const CMD  = APP_PATH . 'vendor/bin/phpcs --standard=' . SCRIPT_ROOT . 'ruleset.xml -s {{ PATH }}';
    public const ISPHP = true;

    public $violations_only = true;
}
