<?php
/**
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @copyright Oleg Stepura <github@oleg.stepura.com>
 * @version $Id$
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Pagerfanta\Adapter\AdapterInterface;
use Doctrine\ORM\QueryBuilder as Builder;
use Doctrine\ORM\EntityManager;

/**
 * PagerfantaAdapter class.
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @version 1.0
 */
class PagerfantaAdapter implements AdapterInterface
{
	/**
	 * @var \Doctrine\ORM\Query
	 */
	protected $query;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @param \Doctrine\ORM\QueryBuilder $query
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param string $className
	 */
	public function __construct(Builder $query, EntityManager $entityManager, $className)
	{
		$this->query = $query;
		$this->em = $entityManager;
		$this->className = $className;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNbResults()
	{
		$id = $this->em->getClassMetadata($this->className)->getSingleIdentifierFieldName();

		$query = clone $this->query;
		$query->setParameters($this->query->getParameters());
		$query->select("count(entity.$id)");

		$query->setParameters($this->query->getParameters());

		return (int) $query->getQuery()->getSingleScalarResult();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSlice($offset, $length)
	{
		$query = clone $this->query;
		$query->setParameters($this->query->getParameters());

		$query->setFirstResult($offset)->setMaxResults($length);

		return $query->getQuery()->getResult();
	}
}
