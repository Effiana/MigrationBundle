<?php

namespace Effiana\MigrationBundle\Fixture;

interface VersionedFixtureInterface
{
    /**
     * Return current fixture version
     *
     * @return string
     */
    public function getVersion();
}
