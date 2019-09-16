<?php

namespace Effiana\MigrationBundle\Event;

class PreMigrationEvent extends MigrationEvent
{
    /**
     * @var array
     *      key   = bundle name
     *      value = version
     */
    protected $loadedVersions = [];

    /**
     * Gets a list of the latest loaded versions for all bundles
     *
     * @return array
     *      key   = bundle name
     *      value = version
     */
    public function getLoadedVersions(): array
    {
        return $this->loadedVersions;
    }

    /**
     * Gets the latest version loaded version of the given bundle
     *
     * @param string $bundleName
     * @return string|null
     */
    public function getLoadedVersion($bundleName): ?string
    {
        return $this->loadedVersions[$bundleName] ?? null;
    }

    /**
     * Sets a number of already loaded version of the given bundle
     *
     * @param string $bundleName
     * @param string $version
     */
    public function setLoadedVersion($bundleName, $version): void
    {
        $this->loadedVersions[$bundleName] = $version;
    }
}
