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
 * @phpcs:disable Magento2.Files.LineLength.MaxExceeded
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */

namespace HP\BestPractice\Task;

class HeaderComments extends AbstractTask
{
    public const NAME = 'Header Comments';

    public const DESC = 'Ensures php header comments reflect our coding standard';

    // phpcs:disable Magento2.Files.LineLength.MaxExceeded
    // phpcs:disable Generic.Files.LineLength.TooLong
    protected $regexp = [
        "php" => "\<\?php\n\/\*\*\n( *)\* \@package( *)(.*)\n( *)\* \@version( *)(.*)\n( *)\* \@author( *)Blue Acorn iCi <code@blueacorn.com>\n(.*)(\* \@author( *).* <(.*)>\n)*( *)\* \@copyright( *)Copyright © ( *).*\. All rights reserved\.?\n( *)\*\/",
        "phtml" => "\<\?php\n\/\*\*\n( *)\* \@package( *)(.*)\n( *)\* \@version( *)(.*)\n( *)\* \@author( *)Blue Acorn iCi <code@blueacorn.com>\n(.*)(\* \@author( *).* <(.*)>\n)*( *)\* \@copyright( *)Copyright © ( *).*\. All rights reserved\.?\n( *)\*\/",
        "xml" => "\/\*\*\n( *)\* \@package( *)(.*)\n( *)\* \@version( *)(.*)\n( *)\* \@author( *)Blue Acorn iCi <code@blueacorn.com>\n(.*)(\* \@author( *).* <(.*)>\n)*( *)\* \@copyright( *)Copyright © ( *).*\. All rights reserved\.?\n( *)\*\/"
    ];
    // phpcs:enable

    protected $allowedExtensions = ['php', 'phtml', 'xml'];

    /**
     * @param string $path
     * @param array $result
     *
     * @return array
     */
    protected function processPath($path, $result)
    {
        $DirectorySearch = new \HP\BestPractice\DirectorySearch();
        foreach ($DirectorySearch->rsearch($path, $this->allowedExtensions) as $file) {
            $result = $this->validateFile($file, $result);
        }
        return $result;
    }

    /**
     * @param mixed $file
     * @param array $return
     *
     * @return array
     */
    protected function validateFile($file, $return)
    {
        $this->tries++;
        if (is_object($file)) {
            $file = $file->getRealPath();
        }

        //phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        $contents = file_get_contents($file);
        //phpcs:enable

        $fileData = explode('.', $file);
        $extension = end($fileData);
        $namespace = $this->getNamespace($file);

        if ($extension && isset($this->regexp[$extension])) {
            if (!preg_match("/" . $this->regexp[$extension] . "/Uis", $contents, $matches)) {
                $this->result = false;
                $this->failures++;

                $this->output[] = [
                    'msg' => (string)$file . " failed validation! \n",
                    'ok' => false
                ];
            } else {
                if ($namespace === false ||
                    preg_match(
                        "/\@package( *)" .
                        implode("|", $this->getNamespaces()) .
                        "(.?)" .
                        preg_quote($namespace, '/') .
                        "/i",
                        $matches[0]
                    )
                ) {
                    $this->successes++;
                    $this->output[] = (string)$file . " passed validation! \n";
                } else {
                    $this->result = false;
                    $this->failures++;

                    $this->output[] = [
                        'msg' => (string)$file . " is applied with invalid namespace! \n",
                        'ok' => false
                    ];
                }
            }
        } else {
            if (!in_array($extension, $this->allowedExtensions, true)) {
                $this->successes++;
                $this->output[] = (string)$file . " passed validation! \n";
            } else {
                $this->result = false;
                $this->failures++;
                $this->output[] = [
                    'msg' => (string)$file . " unsupported extension! \n",
                    'ok' => false
                ];
            }
        }
        return $return;
    }

    /**
     * Get Array of namespace
     *
     * @return array
     */
    protected function getNamespaces()
    {
        $runner = new \HP\BestPractice\TestRunner();
        $magentoSpecs = $runner->getMagentoSpecsConfig();

        $namespaces = [];
        foreach ($magentoSpecs as $specs) {
            //phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $namespaces = array_merge(array_keys($specs), $namespaces);
            //phpcs:enable
        }
        return array_unique($namespaces);
    }

    /**
     * Execute script
     *
     * @return void
     */
    public function execute()
    {
        if ($this->files === null) {
            // Process all folders
            $filesystem = new \Symfony\Component\Filesystem\Filesystem();

            if ($this->paths !== null) {
                foreach ($this->paths as $path) {
                    if ($filesystem->exists($path)) {
                        $this->output += $this->processPath($path, $this->output);
                    }
                }
            }
        } else {
            foreach ($this->files as $file) {
                $this->output += $this->validateFile($file, $this->output);
            }
        }
    }

    /**
     * @param string $file
     *
     * @return bool|string
     */
    protected function getNamespace($file)
    {
        $fileData = explode('/', $file);
        $replace = false;
        $key = array_search('code', $fileData);
        if ($key === false) {
            $key = array_search('vendor', $fileData);
            $replace = true;
        }
        if ($key !== false && isset($fileData[$key + 2])) {
            $namespace = $fileData[$key + 2];
            if ($replace) {
                $namespace = str_replace('module-', '', $namespace);
            }
            return $namespace;
        }
        return false;
    }
}
