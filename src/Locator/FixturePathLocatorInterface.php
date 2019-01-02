<?php

namespace Effiana\MigrationBundle\Locator;

/**
 * Give interface for Path Locators.
 *
 * @package Effiana\MigrationBundle\Locator
 */
interface FixturePathLocatorInterface
{
    /**
     * @param string $fixtureType
     *
     * @return string
     */
    public function getPath(string $fixtureType): string;
}
