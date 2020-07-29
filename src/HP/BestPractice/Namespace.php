<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace Magento\Framework\Component;

class ComponentRegistrar
{

    /**#@+
     * Different types of components
     */
    const MODULE = 'module';
    const LIBRARY = 'library';
    const THEME = 'theme';
    const LANGUAGE = 'language';
    const SETUP = 'setup';

    /**
     * @return true
     */
    public function register()
    {
        return true;
    }
}
