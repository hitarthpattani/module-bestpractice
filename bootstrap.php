<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
error_reporting(E_ALL);
date_default_timezone_set('UTC');

require_once __DIR__ . '/autoload.php';

use HP\BestPractice\Test;

return new Test();

// use HP\BestPractice\App\Container;

// return new Container(BESTPRACTICE_BP, BP);
