<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice;

class DirectorySearch
{
    /**
     * @param string $folder
     * @param array $pattern_array
     *
     * @return array
     */
    public function rsearch($folder, $pattern_array)
    {
        $return = [];
        $iti = new \RecursiveDirectoryIterator($folder);
        foreach (new \RecursiveIteratorIterator($iti) as $file) {
            $tmp = explode('.', $file);
            if (in_array(strtolower(array_pop($tmp)), $pattern_array)) {
                $return[] = $file;
            }
        }
        return $return;
    }
}
