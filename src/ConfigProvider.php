<?php

namespace Dms\Cli\Expressive;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'console' => [
                'commands' => [
                    File\ClearTempFilesCommand::class,
                    Install\DmsInstallCommand::class,
                    Migrations\AutoGenerateMigrationCommand::class,
                    Migrations\MigrateCommand::class,
                    Migrations\FreshCommand::class,
                    Migrations\InstallCommand::class,
                    Migrations\RefreshCommand::class,
                    Migrations\ResetCommand::class,
                    Migrations\RollbackCommand::class,
                    Migrations\StatusCommand::class,
                    Scaffold\ScaffoldCmsCommand::class,
                    Scaffold\ScaffoldPersistenceCommand::class,
                ],
            ],
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [];
    }
}
