<?php
/**
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @copyright Oleg Stepura <github@oleg.stepura.com>
 * @version $Id: QueryBuilder.php,v 461d35d91fc4 2011/09/30 16:05:50 cds $
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\Common\Annotations\Reader;

/**
 * QueryBuilder class.
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @version 1.0
 */
class QueryBuilder
{
	/**
	 * Entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * Columns to search at
	 * @var array
	 */
	protected $searchColumns = array();

	/**
	 * Entity name
	 * @var string
	 */
	protected $entityName;

	/**
	 * @var string
	 */
	protected $request;

	/**
	 * @var string
	 */
	protected $idName;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 * @param string $className
	 */
	public function __construct(
		EntityManager $entityManager,
		Reader $reader,
		$className
	)
	{
		$this->entityName = $className;
		$this->entityManager = $entityManager;

		/** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
		$metadata = $entityManager->getClassMetadata($className);
		/** @var $reflectionClass \ReflectionClass */
		$reflectionClass = $metadata->getReflectionClass();
		$this->idName = $metadata->getSingleIdentifierFieldName();

		foreach ($reflectionClass->getProperties() as $property) {
			foreach ($reader->getPropertyAnnotations($property) as $annotation) {
				if ($annotation instanceof Searchable) {
					$this->searchColumns[] = $property->getName();
				}
			}
		}
	}

	/**
	 * @param string $searchQuery
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function createDoctrineQueryBuilder($searchQuery)
	{
		$searchQuery = str_replace('*', '%', $searchQuery);

		$qb = $this->entityManager->createQueryBuilder();

		$searchQueryParts = explode(' ', $searchQuery);

		$query = $qb
			->select('entity')
			->from($this->entityName, 'entity');

		$subquery = null;
		$subst = 'a';

		foreach ($searchQueryParts as $i => $searchQueryPart) {
			$qbInner = $this->entityManager->createQueryBuilder();

			$paramPosistion = $i + 1;
			++$subst;

			$whereQuery = $qb->expr()->orX();

			foreach ($this->searchColumns as $column) {
				$whereQuery->add($qb->expr()->like(
					$subst . '.' . $column,
					'?' . $paramPosistion
				));
			}

			$subqueryInner = $qbInner
				->select($subst . '.' . $this->idName)
				->from($this->entityName, $subst)
				->where($whereQuery);

			if ($subquery != null) {
				$subqueryInner->andWhere(
					$qb->expr()->in(
						$subst . '.' . $this->idName,
						$subquery->getQuery()->getDql()
					)
				);
			}

			$subquery = $subqueryInner;

			$query->setParameter($paramPosistion, $searchQueryPart);
		}

		$query->where(
			$qb->expr()->in(
				'entity.' . $this->idName,
				$subquery->getQuery()->getDql()
			)
		);

		return $query;
	}

	/**
	 * @param string $searchQuery
	 * @return \Pagerfanta\Adapter\DoctrineORMAdapter
	 */
	public function getPagerfantaAdapter($searchQuery)
	{
		return new PagerfantaAdapter(
			$this->createDoctrineQueryBuilder($searchQuery),
			$this->entityManager,
			$this->entityName
		);
	}

	/**
	 * @param string $searchQuery
	 * @return \Pagerfanta\Pagerfanta
	 */
	public function getPagerfanta($searchQuery)
	{
		return new Pagerfanta($this->getPagerfantaAdapter($searchQuery));
	}
}
