<?php
/**
 * @package     HP\BestPractice
 * @version     0.1.0
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © 2019. All rights reserved.
 */

namespace HP\BestPractice;

class NameSpaceFinder
{
    private $namespaceMap = [];

    private $defaultNamespace = 'global';

    /**
     * NameSpaceFinder constructor.
     */
    public function __construct()
    {
        $this->traverseClasses();
    }

    /**
     * @param mixed $class
     *
     * @return string
     * @throws \ReflectionException
     */
    private function getNameSpaceFromClass($class)
    {
        // Get the namespace of the given class via reflection.
        // The global namespace (for example PHP's predefined ones)
        // will be returned as a string defined as a property ($defaultNamespace)
        // own namespaces will be returned as the namespace itself

        $reflection = new \ReflectionClass($class);
        return $reflection->getNameSpaceName() === ''
            ? $this->defaultNamespace
            : $reflection->getNameSpaceName();
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function traverseClasses()
    {
        // Get all declared classes
        $classes = get_declared_classes();

        foreach ($classes as $class) {
            // Store the namespace of each class in the namespace map
            $namespace = $this->getNameSpaceFromClass($class);
            $this->namespaceMap[ $namespace ][] = $class;
        }
    }

    /**
     * @return array
     */
    public function getNameSpaces()
    {
        return array_keys($this->namespaceMap);
    }

    /**
     * @param string $namespace
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getClassesOfNameSpace($namespace)
    {
        if (!isset($this->namespaceMap[ $namespace ])) {
            throw new \InvalidArgumentException('The Namespace ' . $namespace . ' does not exist');
        }

        return $this->namespaceMap[ $namespace ];
    }
}
