<?php

namespace Effiana\MigrationBundle\Twig;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides a Twig function used in generator of data migration classes:
 *   - oro_migration_get_schema_column_options
 */
class SchemaDumperExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /** @var AbstractPlatform */
    protected $platform;
    /** @var Column */
    protected $defaultColumn;
    /** @var array */
    protected $defaultColumnOptions = [];
    /** @var array */
    protected $optionNames = [
        'default',
        'notnull',
        'length',
        'precision',
        'scale',
        'fixed',
        'unsigned',
        'autoincrement'
    ];
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * {@inheritdoc}
     */
    public function getName(): String
    {
        return 'schema_dumper_extension';
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('oro_migration_get_schema_column_options', [$this, 'getColumnOptions']),
        ];
    }

    /**
     * @param Column $column
     * @return array
     * @throws DBALException
     */
    public function getColumnOptions(Column $column): array
    {
        $defaultOptions = $this->getDefaultOptions();
        $platform = $this->getPlatform();
        $options = [];
        foreach ($this->optionNames as $optionName) {
            $value = $this->getColumnOption($column, $optionName);
            if ($value !== $defaultOptions[$optionName]) {
                $options[$optionName] = $value;
            }
        }
        $comment = $column->getComment();
        if ($platform && $platform->isCommentedDoctrineType($column->getType())) {
            $comment .= $platform->getDoctrineTypeComment($column->getType());
        }
        if (!empty($comment)) {
            $options['comment'] = $comment;
        }
        return $options;
    }
    /**
     * @param Column $column
     * @param string $optionName
     * @return mixed
     */
    protected function getColumnOption(Column $column, $optionName)
    {
        $method = 'get' . $optionName;
        return $column->$method();
    }

    /**
     * @return AbstractPlatform
     * @throws DBALException
     */
    protected function getPlatform(): AbstractPlatform
    {
        if (!$this->platform) {
            $this->platform = $this->entityManager
                ->getConnection()
                ->getDatabasePlatform();
        }
        return $this->platform;
    }

    /**
     * @return array
     * @throws DBALException
     */
    protected function getDefaultOptions(): array
    {
        if (!$this->defaultColumn) {
            $this->defaultColumn = new Column('_template_', Type::getType(Type::STRING));
        }
        if (!$this->defaultColumnOptions) {
            foreach ($this->optionNames as $optionName) {
                $this->defaultColumnOptions[$optionName] = $this->getColumnOption($this->defaultColumn, $optionName);
            }
        }
        return $this->defaultColumnOptions;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return [
            'doctrine' => EntityManagerInterface::class,
        ];
    }
}