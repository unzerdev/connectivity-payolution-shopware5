<?php

namespace PolPaymentPayolution\Payment\SaveHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;

/**
 * Trait QueryBuilderTrait
 *
 * @package PolPaymentPayolution\Payment\SaveHandler
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
trait QueryBuilderTrait
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $tableAlias;

    protected $fields = [];

    /**
     * Build Base Query
     *
     * @return QueryBuilder
     */
    protected function buildBaseQuery()
    {
        $qb = $this->connection->createQueryBuilder();

        if (count($this->getCurrentTableData()) === 0) {
            $qb->insert($this->tableName);
        } else {
            $this->removeOldData();
            $qb->insert($this->tableName);
        }

        return $qb;
    }

    /**
     * Remove old Data
     *
     * @return void
     */
    protected function removeOldData()
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->delete($this->tableName)
            ->where($qb->expr()->eq('userId ', ':userId'))
            ->setParameter('userId', $this->getUserId());

        $qb->execute();
    }

    /**
     * Add Element to Query
     *
     * @param QueryBuilder $queryBuilder
     * @param string $key
     * @param $value
     * @return void
     */
    protected function addSetElementQuery(QueryBuilder $queryBuilder, $key, $value)
    {
        if (strpos($queryBuilder->getSQL(), 'INSERT') === 0) {
            $values = $queryBuilder->getQueryPart('values');
            $values[$key] = sprintf(':%s', $key);

            $queryBuilder->values($values);
        } else {
            $queryBuilder->set($key, sprintf(':%s', $key));
        }

        $queryBuilder->setParameter($key, $value);
    }

    /**
     * Execute Query
     *
     * @param QueryBuilder $queryBuilder
     * @return bool
     */
    protected function executeQuery(QueryBuilder $queryBuilder)
    {
        if ($queryBuilder->execute() > 0) {
            return true;
        }

        return $this->getCurrentTableData() === $this->getCurrentTableData(true);
    }

    /**
     * Get Current Table Data
     *
     * @param bool $force
     * @return array
     */
    protected function getCurrentTableData($force = false)
    {
        static $tableInfo = [];

        if (!$tableInfo || $force) {
            $qb = $this->connection->createQueryBuilder();
            $qb->from($this->tableName, $this->tableAlias)
                ->where($qb->expr()->eq(sprintf('%s.userId', $this->tableAlias), ':userId'))
                ->setParameter('userId', $this->getUserId());

            if (count($this->fields) === 0) {
                $qb->select('*');
            } else {
                $select = '';
                foreach ($this->fields as $field) {
                    $select .= sprintf('%s.%s', $this->tableAlias, $field);
                }
                ltrim(',');
            }

            $result = $qb->execute()->fetchAll(PDO::FETCH_ASSOC);

            if (isset($result[0])) {
                $tableInfo =  $result[0];
            }
        }

        return $tableInfo;
    }

    /**
     * Get User Id
     *
     * @return string|null
     */
    abstract protected function getUserId();
}
