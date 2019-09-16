<?php
/**
 * This file is part of the Effiana package.
 *
 * (c) Effiana, LTD
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */
declare(strict_types=1);

namespace Effiana\MigrationBundle\Container;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\Container as DependencyInjectionContainer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Provides access to the private services in the migrations and fixtures.
 * Must be used carefully and only for migration loading.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class MigrationContainer extends DependencyInjectionContainer
{
    /** @var DependencyInjectionContainer */
    private $publicContainer;
    /** @var PsrContainerInterface */
    private $privateContainer;
    /**
     * @param ParameterBagInterface|null $parameterBag
     * @param DependencyInjectionContainer $publicContainer
     * @param PsrContainerInterface $privateContainer
     */
    public function __construct(
        ?ParameterBagInterface $parameterBag,
        DependencyInjectionContainer $publicContainer,
        PsrContainerInterface $privateContainer
    ) {
        $parameterBag = $parameterBag ?? $publicContainer->getParameterBag();
        parent::__construct($parameterBag);
        $this->publicContainer = $publicContainer;
        $this->privateContainer = $privateContainer;
    }

    /**
     *
     */
    public function compile(): void
    {
        $this->publicContainer->compile();
    }

    /**
     * @return bool
     */
    public function isCompiled(): bool
    {
        return $this->publicContainer->isCompiled();
    }

    /**
     * @param string $id
     * @param object|null $service
     */
    public function set($id, $service): void
    {
        $this->publicContainer->set($id, $service);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->publicContainer->has($id) || $this->privateContainer->has($id);
    }

    /**
     * @param string $id
     * @param int $invalidBehavior
     * @return mixed|object|null
     * @throws \Exception
     */
    public function get($id, $invalidBehavior = /* self::EXCEPTION_ON_INVALID_REFERENCE */ 1)
    {
        return $this->privateContainer->has($id)
            ? $this->privateContainer->get($id)
            : $this->publicContainer->get($id, $invalidBehavior);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function initialized($id): bool
    {
        return $this->publicContainer->initialized($id);
    }
    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->publicContainer->reset();
    }

    /**
     * @return string[]
     */
    public function getServiceIds(): array
    {
        return $this->publicContainer->getServiceIds();
    }

    /**
     * @return array
     */
    public function getRemovedIds(): array
    {
        return $this->publicContainer->getRemovedIds();
    }
}