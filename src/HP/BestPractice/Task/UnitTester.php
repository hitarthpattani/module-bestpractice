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

class UnitTester extends AbstractTask
{
    public const NAME = 'Unit Tests';

    public const DESC = 'Runs any relevant phpunit tests to paths';

    public const CMD = APP_PATH . 'vendor/bin/phpunit {{ PATH }}';

    public const ISPHP = true;

    /**
     * Execute script
     *
     * @return void
     */
    public function execute()
    {
        if ($this->paths === null) {
            if ($this->files !== null) {
                $this->paths = $this->extractTestsPaths($this->files);
            } else {
                return;
            }
        }

        // Process all folders
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        foreach ($this->paths as $path) {
            if ($filesystem->exists($path)) {
                $this->output += $this->processPath($path, $this->output);
            }
        }
    }

    /**
     * Validate any tests folders from changed files directories chain,
     * so only tests in affected extensions are run here.
     *
     * @param array $files
     *
     * @return array
     */
    protected function extractTestsPaths($files)
    {
        $result = [];
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();
        foreach ($files as $file) {
            // do not dive deeper than out APP_PATH
            $file = str_replace(APP_PATH, '', $file);
            $file = explode(DIRECTORY_SEPARATOR, $file);
            array_pop($file);
            $size = count($file);
            while ($size >= 0) {
                $testsPath = APP_PATH . implode(DIRECTORY_SEPARATOR, $file) . DIRECTORY_SEPARATOR . 'tests';
                $size--;
                if ($filesystem->exists($testsPath)) {
                    $result[] = $testsPath;
                    break;
                }
                array_pop($file);
            }
        }
        $result = array_unique($result);
        return $result;
    }
}
