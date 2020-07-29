<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace HP\BestPractice\App;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\BufferIO;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Exception;

/**
 * @inheritdoc
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Container implements ContainerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param string $toolsBasePath
     * @param string $magentoBasePath
     * @throws ContainerException
     */
    public function __construct(string $toolsBasePath, string $magentoBasePath)
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->set('container', $this);
        $containerBuilder->setDefinition('container', new Definition(__CLASS__))
            ->setArguments([$toolsBasePath, $magentoBasePath]);

        $this->container = $containerBuilder;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        try {
            return $this->container->get($id);
        } catch (Exception $exception) {
            throw new ContainerException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @inheritdoc
     */
    public function set(string $id, $service): void
    {
        $this->container->set($id, $service);
    }

    /**
     * @inheritdoc
     */
    public function create(string $abstract, array $params = [])
    {
        if (empty($params) && $this->has($abstract)) {
            return $this->get($abstract);
        }

        return new $abstract(...array_values($params));
    }
}
