<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace HP\BestPractice;

use Composer\Composer;
use HP\BestPractice\App\ContainerInterface;
use HP\BestPractice\App\Container;
// use HP\BestPractice\Command;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var ContainerInterface|Container
     */
    private $container;

    /**
     * @param ContainerInterface|Container $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(
            $container->create(Composer::class)->getPackage()->getPrettyName(),
            $container->create(Composer::class)->getPackage()->getPrettyVersion()
        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            // $this->container->create(Command\Build::class),
            // $this->container->create(Command\Build\Generate::class),
            // $this->container->create(Command\Build\Transfer::class),
            // $this->container->create(Command\Deploy::class),
            // $this->container->create(Command\ConfigDump::class),
            // $this->container->create(Command\DbDump::class),
            // $this->container->create(Command\PostDeploy::class),
            // $this->container->create(Command\BackupRestore::class),
            // $this->container->create(Command\BackupList::class),
            // $this->container->create(Command\ApplyPatches::class),
            // $this->container->create(Command\Dev\UpdateComposer::class),
            // $this->container->create(Command\Wizard\ScdOnDemand::class),
            // $this->container->create(Command\Wizard\ScdOnBuild::class),
            // $this->container->create(Command\Wizard\ScdOnDeploy::class),
            // $this->container->create(Command\ModuleRefresh::class),
            // $this->container->create(Command\Wizard\IdealState::class),
            // $this->container->create(Command\Wizard\MasterSlave::class),
            // $this->container->create(Command\Wizard\SplitDbState::class),
            // $this->container->create(Command\CronDisable::class),
            // $this->container->create(Command\CronEnable::class),
            // $this->container->create(Command\CronKill::class),
            // $this->container->create(Command\CronUnlock::class),
            // $this->container->create(Command\ConfigShow::class),
            // $this->container->create(Command\RunCommand::class),
            // $this->container->create(Command\GenerateSchema::class)
        ]);
    }
}