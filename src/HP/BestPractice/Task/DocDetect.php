<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */
/**
 * @phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
 */

namespace HP\BestPractice\Task;

use PHPUnit\Framework\TestCase;

/**
 * Class RequiredFiles
 */
class DocDetect extends AbstractTask
{
    public const NAME = 'Documentation Detector';
    public const DESC = 'Looks for Blue Acorn specific UAT and developer documentation';
    public $violations_only = false;

    public $isphpunit = true;

    /**
     * @return bool
     */
    public function testReadmeMdExists()
    {
        return $this->assertFileExistsInDirs(
            $this->paths,
            'docs/README.md'
        );
    }

    /**
     * @return bool
     */
    public function testUatMdExists()
    {
        return $this->assertFileExistsInDirs(
            $this->paths,
            'docs/UAT.md'
        );
    }

    /**
     * @param array $dirs
     * @param string $fileName
     * @return bool
     */
    protected function assertFileExistsInDirs($dirs, $fileName)
    {
        $totalResult = true;
        foreach ($dirs as $dir) {
            // We validate all dirs from our paths
            // phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
            if (!is_dir($dir)) {
            // phpcs:enable
                continue;
            }
            $result = true;
            // In each dir we go deeper and select extension dir to validate
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($this->canSkip($fileInfo)) {
                    continue;
                }
                // In case correct extensions dir found and exists - assertion is run
                // phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
                // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                $localResult = (bool) is_file("{$fileInfo->getPathname()}/$fileName");
                // phpcs:enable

                if ($localResult) {
                    $this->output[] = 'asserting ' . "{$fileInfo->getPathname()}/{$fileName} exists...\n";
                } else {
                    $this->output[] = [
                        'msg' => 'asserting ' . "{$fileInfo->getPathname()}/{$fileName}  DO NOT exists...\n",
                        'ok' => false,
                    ];
                }
                $result = $result && $localResult;
            }
            $totalResult = $totalResult && $result;
        }
        $this->output[] = 'Asserting '.$fileName;
        return $totalResult;
    }

    /**
     * @param \DirectoryIterator $fileInfo
     *
     * @return bool
     */
    protected function canSkip($fileInfo)
    {
        // Files and dots are skipped
        if ($fileInfo->isDot() || $fileInfo->isDir() === false) {
            return true;
        }
        // If we are allowed to validate only specific extensions - other folders are considered good.
        if (is_array($this->filteredExtensions) && !isset($this->filteredExtensions[$fileInfo->getBasename()])) {
            return true;
        }
        return false;
    }
}
