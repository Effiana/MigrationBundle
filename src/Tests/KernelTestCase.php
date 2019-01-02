<?php
/**
 * This file is part of the BrandOriented package.
 *
 * (c) Brand Oriented sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */

namespace Effiana\MigrationBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as TestCase;

/**
 * Class KernelTestCase
 * @package AppBundle\Tests
 * @author Francesco Casula <fra.casula@gmail.com>
 */
abstract class KernelTestCase extends TestCase
{


    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        self::$container = null;
        parent::tearDown();
    }

    /**
     * @param array $options
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer(array $options = [])
    {
        if (!self::$container) {
            static::bootKernel($options);
            self::$container = static::$kernel->getContainer();
        }

        return self::$container;
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    protected function getParameter($parameter)
    {
        return $this->getContainer()->getParameter($parameter);
    }
}