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

class CopyPaste extends AbstractTask
{
    public const NAME = 'Copy/Paste Detector';
    public const DESC = 'Runs the bundled phpcpd against BA paths/files';
    public const CMD  = APP_PATH . 'vendor/bin/phpcpd --fuzzy {{ PATH }}';
    public const ISPHP = true;
}
