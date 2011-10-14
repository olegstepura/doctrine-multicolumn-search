<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id: QueryBuilderFactory.php,v 461d35d91fc4 2011/09/30 16:05:50 cds $
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\Reader;

/**
 * QueryBuilderFactory class for handling dependencies.
 * Is intended to be stored in dependency injection container.
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version 1.0
 */
class QueryBuilderFactory
{
	/**
	 * Entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * @var \Doctrine\Common\Annotations\Reader
	 */
	protected $reader;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 */
	public function __construct(
		EntityManager $entityManager,
		Reader $reader
	)
	{
		$this->entityManager = $entityManager;
		$this->reader = $reader;
	}

	/**
	 * @param string $className
	 * @return QueryBuilder
	 */
	public function createBuilder($className)
	{
		return new QueryBuilder(
			$this->entityManager,
			$this->reader,
			$className
		);
	}
}
