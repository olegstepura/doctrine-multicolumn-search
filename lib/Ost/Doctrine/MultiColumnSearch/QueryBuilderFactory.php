<?php
/**
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @copyright Oleg Stepura <github@oleg.stepura.com>
 * @version $Id: QueryBuilderFactory.php,v 461d35d91fc4 2011/09/30 16:05:50 cds $
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\Reader;

/**
 * QueryBuilderFactory class for handling dependencies.
 * Is intended to be stored in dependency injection container.
 * @author Oleg Stepura <github@oleg.stepura.com>
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
