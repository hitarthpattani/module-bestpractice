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

class MessDetector extends AbstractTask
{
    public const NAME = "Mess Detector";
    public const DESC = "Runs the bundled php mess detector against BA paths";
    public const CMD  = APP_PATH . 'vendor/bin/phpmd {{ PATH }} text '.SCRIPT_ROOT.
        'phpmd.xml --exclude vendor/,templates/';
    public $violations_only = true;
    public const ISPHP = true;

    protected $filesSeparator = ',';
}
