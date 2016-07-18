<?php
namespace AppBundle\Repository\Doctrine;


use AppBundle\Repository\EntityCollectionInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class EntityCollection implements EntityCollectionInterface
{
    /**
     * @var bool
     */
    protected $disableUnitOfWork;
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    /**
     * @var int|null
     */
    private $limit;
    /**
     * @var int
     */
    private $offset = 0;

    public function __construct(QueryBuilder $builder, $disableUnitOfWork = false)
    {
        $this->queryBuilder = $builder;
        $this->setLimit($builder->getMaxResults());
        $this->setOffset((int)$builder->getFirstResult());
        $this->disableUnitOfWork = $disableUnitOfWork;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $query = $this->generateBuilder()->getQuery();
        if ($this->disableUnitOfWork) {
            $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        }

        return $query->getResult();
    }

    /**
     * Creates new query builder and applies filters.
     *
     * @return QueryBuilder
     */
    protected function generateBuilder(): QueryBuilder
    {
        $builder = clone $this->queryBuilder;
        $builder->setMaxResults($this->getLimit());
        $builder->setFirstResult($this->getOffset());

        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function setLimit(int $limit = null): EntityCollectionInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): EntityCollectionInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return $this->generateBuilder()->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        $countBuilder = $this->generateBuilder();
        $countBuilder->resetDQLPart('select');
        $countBuilder->select($countBuilder->expr()->countDistinct($countBuilder->getRootAliases()[0]));
        $countBuilder->resetDQLPart('orderBy');
        $countBuilder->setMaxResults(null);
        $countBuilder->setFirstResult(null);

        return $countBuilder->getQuery()->getSingleScalarResult();
    }
}