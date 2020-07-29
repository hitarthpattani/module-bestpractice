<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice;

class TestPathFinder
{
    /**
     * @param array $runnerConfig
     * @param array $magentoConfig
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     *
     * @return array
     */
    public function getMagentoTestPaths($runnerConfig, $magentoConfig, $filesystem)
    {
        $diffPath = $filesystem->exists(APP_PATH . "__diff/")? '__diff/': '';

        $paths = [];

        $namespaceKeys = [
            'Module_Namespace' => [$diffPath . 'app/code/'],
            'Theme_Namespace' => [$diffPath . 'app/design/frontend/', $diffPath . 'app/design/adminhtml/']
        ];

        foreach ($runnerConfig as $test => $config) {
            $paths[$test]['config'] = $config;
            foreach ($namespaceKeys as $key => $keyPaths) {
                if (isset($magentoConfig[$key])) {
                    foreach ($this->getSpecificNamespacePath($magentoConfig[$key], $keyPaths, $test) as $path) {
                        $paths[$test]['paths'][] = $path;
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * @param array $namespaceValues
     * @param array $keyPaths
     * @param string $test
     *
     * @return array
     */
    public function getSpecificNamespacePath($namespaceValues, $keyPaths, $test)
    {
        $paths = [];

        foreach ($namespaceValues as $namespace => $namespaceConfigs) {
            $isPathInclude = false;
            if (is_array($namespaceConfigs)) {
                if (in_array($test, array_keys($namespaceConfigs))) {
                    $isPathInclude = $namespaceConfigs[$test];
                }
            } elseif (is_bool($namespaceConfigs)) {
                $isPathInclude = $namespaceConfigs;
            }

            if ($isPathInclude) {
                foreach ($keyPaths as $keyPath) {
                    $path = implode("", [APP_PATH, $keyPath, $namespace . '/']);
                    if (!in_array($path, $paths)) {
                        $paths[] = $path;
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     *
     * @return bool
     */
    public function isCustomModulePath($filesystem)
    {
        return $filesystem->exists(APP_PATH . 'vendor/hp/module-bestpractice/src/') &&
            !$filesystem->exists(APP_PATH . "__diff/") &&
            $filesystem->exists(APP_PATH . 'registration.php');
    }

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     *
     * @return bool
     */
    public function isMagentoInstallationPath($filesystem)
    {
        return $filesystem->exists(APP_PATH . 'vendor/hp/module-bestpractice/src/');
    }

    /**
     * @param array $runnerConfig
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param bool $customModule
     *
     * @return array
     */
    public function getModuleTestPaths($runnerConfig, $filesystem, $customModule = false)
    {
        $paths = [];

        foreach ($runnerConfig as $test => $config) {
            $paths[$test]['config'] = $config;
            if ($customModule) {
                $finder = new \Symfony\Component\Finder\Finder();
                foreach ($finder->directories()->in(APP_PATH)->depth('== 0') as $dir) {
                    if ($filesystem->exists(APP_PATH . $dir->getRelativePathname())
                        && $dir->getRelativePathname() != 'vendor'
                        && $dir->getRelativePathname() != '.'
                        && $dir->getRelativePathname() != '..') {
                        $paths[$test]['paths'][] = APP_PATH . $dir->getRelativePathname();
                    }
                }
            } else {
                $paths[$test]['paths'] = [
                    APP_PATH . 'HP/',
                    APP_PATH . 'Test/',
                    APP_PATH . 'tests/'
                ];
            }
        }

        return $paths;
    }
}
