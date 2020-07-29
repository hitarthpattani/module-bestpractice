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

abstract class AbstractTask
{
    public const NAME = 'Unnamed Test Task';
    public const DESC = 'This is a short description of the test.';
    public const MOREINFO = '';
    public const CMD = "";
    public const ISPHP = false;

    public $result = true;
    public $tries = 0;
    public $successes = 0;
    public $failures = 0;
    public $violations_only = false;
    public $violations = 0;
    public $info_only = false;
    public $output = [];

    public $paths;
    public $files;

    public $isphpunit = false;

    protected $filesSeparator = ' ';

    protected $php_exec_prefix = 'php -d auto_prepend_file="'.SCRIPT_ROOT.'HP/BestPractice/Constants.php" ';

    /**
     * A list of changed extensions to check, like "/full/path/to/HP/BestPractice"
     * Used by unit tests for now
     *
     * @var null|array
     */
    protected $filteredExtensions = null;

    /**
     * Apply a number of paths to process
     *
     * @param array $paths
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        return $this;
    }

    /**
     * Apply a number of files to process
     *
     * @param array $filesList
     *
     * @return $this
     */
    public function setFiles($filesList)
    {
        $this->files = $filesList;
        return $this;
    }

    /**
     * Execute script
     *
     * @return void
     */
    public function execute()
    {
        if ($this->isphpunit) {
            $this->filterChangedExtensions($this->paths, $this->files);
            $this->executeUnitTests();
            return;
        }

        if ($this->files === null) {
            // Process all folders
            $filesystem = new \Symfony\Component\Filesystem\Filesystem();

            if ($this->paths === null) {
                return;
            }

            foreach ($this->paths as $path) {
                if ($filesystem->exists($path)) {
                    $this->output += $this->processPath($path, $this->output);
                }
            }
        } else {
            $this->output += $this->processPath(implode($this->filesSeparator, $this->files), $this->output);
        }
    }

    /**
     * Runs PhpUnit Style Tests, kinda
     *
     * @return void
     */
    protected function executeUnitTests()
    {
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'test') === 0) {
                if (!$this->info_only) {
                    $this->tries++;
                }

                if ($this->$method()) {
                    $this->output[] = [
                        'msg' => "Success!\n",
                        'ok'  => true
                    ];
                    $this->result = true;
                    $this->successes++;
                } else {
                    $this->output[] = [
                        'msg' => "Failed\n",
                        'ok'  => false
                    ];
                    $this->result = false;
                    $this->failures++;
                }
            }
        }
    }

    /**
     * @param string $path
     * @param array $return
     *
     * @return array
     */
    protected function processPath($path, $return)
    {
        if (!$this->info_only) {
            $this->tries++;
        }

        $cmd = $this::CMD;
        $cmd = str_replace('{{ PATH }}', $path, $cmd);

        $this->output[] = [
            'msg' => (string)'Running ' . $cmd . "\n",
            'ok'  => true
        ];

        if ($this::ISPHP) {
            $cmd = $this->php_exec_prefix.$cmd;
        }
        exec($cmd, $output, $returnVal);

        if ($returnVal !== 0 && empty($output)) {
            $this->output[] = [
                'msg' => "Something failed, please check command locally.\n",
                'ok'  => false
            ];

            // if (!$this->info_only) {
                $this->result = false;
                $this->failures++;
            // }

            return $return;
        }

        foreach ($output as $line) {
            $this->output[] = [
                'msg' => (string)$line . "\n",
                'ok'  => (bool)$returnVal
            ];
            // $this->violations++;
        }

        if (!$returnVal) {
            if (!$this->info_only) {
                $this->successes++;
            }
        } else {
            if (!$this->info_only) {
                $this->result = false;
                $this->failures++;
            }
        }
        return $return;
    }

    /**
     * Marks some validations to be limited to only changed extensions
     *
     * @param array $paths
     * @param array $changedFiles
     * @return void
     */
    protected function filterChangedExtensions($paths, $changedFiles)
    {
        if ($changedFiles === null) {
            return;
        }
        $this->filteredExtensions = [];

        if ($paths !== null) {
            foreach ($paths as $testPath) {
                foreach ($changedFiles as $key => $file) {
                    if (strpos($file, $testPath) !== 0) {
                        continue;
                    }
                    // Remove file from further processing
                    unset($changedFiles[ $key ]);
                    $folder = str_replace($testPath, '', $file);
                    $folder = explode(DIRECTORY_SEPARATOR, $folder);
                    $namespace = array_shift($folder);
                    // phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
                    if ($namespace && is_dir($testPath . $namespace)) {
                    // phpcs:enable
                        $this->filteredExtensions[$namespace] = $namespace;
                    }
                }
            }
        }
    }
}
