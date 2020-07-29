<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */
/**
 * @phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
 * @phpcs:disable Magento2.PHP.LiteralNamespaces.LiteralClassUsage
 */

namespace HP\BestPractice;

use Symfony\Component\Yaml\Yaml;
use HP\BestPractice\Task\AbstractTask;

class TestRunner
{
    public $exitcode = 0;

    public $total_ran = 0;

    public $total_pass = 0;

    public $total_fail = 0;

    public $total_successes = 0;

    public $total_failure = 0;

    public $total_failure_ignored = 0;

    public $total_violations = 0;

    public $total_violations_ignored = 0;

    public $results = [];
    
    protected $files = null;

    protected $paths = null;

    public $output = '';

    /**
     * Short description
     *
     * @param string|array $msg
     * @param string $fgcolor
     * @param string $bgcolor
     * @return void
     * @throws \Exception
     */
    protected function output($msg, $fgcolor = 'none', $bgcolor = 'none')
    {

        if (is_array($msg)) {
            $fgcolor = 'none';
            $bgcolor = 'none';
            if (!$msg['ok']) {
                $fgcolor = 'lightred';
            }
            $msg = (string)$msg['msg'];
        }

        $fgs = [
            'red'      => '0;31',
            'green'    => '0;32',
            'yellow'   => '1;33',
            'lightred' => '1;31',
            'brown'    => '0;33',
            'none'     => '1'
        ];

        $bgs = [
            'black' => '40',
            'none'  => '1'
        ];

        $this->output .= "\e[" . $fgs[ $fgcolor ] . ";" . $bgs[ $bgcolor ] . "m" . $msg . "\e[0m";
    }

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
     * Triggers the test runner, does the thing
     *
     * @param string $testName
     * @param array $config
     *
     * @return void
     * @throws \Exception
     */
    public function run($testName, $config = [])
    {
        $testName = str_replace("/", "", $testName);

        /** @var AbstractTask $test */
        $test = new $testName();
        $test->setPaths($this->paths)
            ->setFiles($this->files)
            ->execute();

        $this->output($test::NAME . "\n\n", 'yellow');

        foreach ($test->output as $line) {
            $this->output("\t");
            $this->output($line);
        }

        if ($test->violations > 0 || $test->violations_only) {
            $this->output("\n\t" . $test->violations . " violations were detected.\n ");
        }

        $this->output("\n\t" . $test->successes . "/" . $test->tries . " completed successfully. ");

        if ($test->result) {
            $this->output("Passing!", 'green');
        } elseif (isset($config['require-passing']) && !$config['require-passing']) {
            $this->output("Ignoring.", 'yellow');
        } else {
            $this->output("Failing.", 'red');
        }

        $this->output("\n\n");

        $this->total_ran += $test->tries;
        $this->total_successes += $test->successes;
        if (isset($config['require-passing']) && $config['require-passing']) {
            $this->total_failure += $test->failures;
            $this->total_violations += $test->violations;
        } else {
            $this->total_failure_ignored += $test->failures;
            $this->total_violations_ignored += $test->violations;
        }

        $this->results[] = $test;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function summary()
    {
        $this->output("\n\n");
        $this->output('Tests complete! ');
        $this->output(
            $this->total_successes . '/' . $this->total_ran . ' completed successfully, and ' .
            $this->total_violations . ' violations were detected. '
        );

        if (!$this->total_failure && ($this->total_violations) === 0) {
            $this->output("All tests passed!\n\n", 'green');
            $this->exitcode = 0;
            return 0;
        }

        $this->output("Some tests failed!\n\n", 'red');
        $this->exitcode = 1;
        return 1;
    }

    /**
     * @param bool $isQuiet
     *
     * @return int
     */
    public function exitCode($isQuiet = false)
    {
        if ($isQuiet === false) {
            return $this->exitcode;
        }

        echo "\nexit successfully flag detected...\n";
        return 0;
    }
    /**
     * Grabs the yaml config
     *
     * @return array json config
     */
    public function getConfig()
    {
        $filename = false;
        
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        if ($filesystem->exists(APP_PATH.'.bestpractices.yml')) {
            $filename = APP_PATH.'.bestpractices.yml';
        } else {
            $filename = SCRIPT_ROOT.'.bestpractices.yml.dist';
        }

        $yaml = new Yaml();

        if (!method_exists($yaml, 'parseFile')) {
            $yaml::parse($filename);
            //phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
            return $yaml::parse(file_get_contents($filename));
            //phpcs:enable
        } else {
            return $yaml::parseFile($filename);
        }
    }
    /**
     * Grabs the yaml for magneto specific config
     *
     * @return array json for magneto specific config
     */
    public function getMagentoSpecsConfig()
    {
        $filename = false;

        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        if ($filesystem->exists(APP_PATH.'.bestpractices.magento.yml')) {
            $filename = APP_PATH.'.bestpractices.magento.yml';
        } else {
            $filename = SCRIPT_ROOT.'.bestpractices.magento.yml.dist';
        }

        $yaml = new Yaml();

        if (!method_exists($yaml, 'parseFile')) {
            $yaml::parse($filename);
            //phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
            return $yaml::parse(file_get_contents($filename));
            //phpcs:enable
        } else {
            return $yaml::parseFile($filename);
        }
    }
}
