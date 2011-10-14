<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id: QueryBuilderTest.php,v 461d35d91fc4 2011/09/30 16:05:50 cds $
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationReader;

require_once __DIR__ . '/User.php';

/**
 * QueryBuilderTest class.
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version 1.0
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
	protected static $em;

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected static function getEntityManager()
	{
		if (null === self::$em) {
			// 'path' is commented out but can be very helpful
			// if used instead of 'memory' for debugging
			$connection = array(
				'driver' => 'pdo_sqlite',
				'memory' => true,
//				'path' => 'database.sqlite',
			);

			$cache = new ArrayCache;
			$config = new Configuration();
			$config->setMetadataCacheImpl($cache);
			$config->setQueryCacheImpl($cache);
			$config->setResultCacheImpl($cache);
			$config->setProxyDir(sys_get_temp_dir());
			$config->setProxyNamespace('DoctrineProxies');
			$config->setAutoGenerateProxyClasses(true);
			$config->setMetadataDriverImpl(new AnnotationDriver(
				new IndexedReader(new AnnotationReader()),
				__DIR__
			));

//			$config->setSQLLogger(new EchoSQLLogger());


			self::$em = EntityManager::create($connection, $config);

			$schemaTool = new SchemaTool(self::$em);

			$cmf = self::$em->getMetadataFactory();
			$classes = $cmf->getAllMetadata();

			$schemaTool->createSchema($classes);
		}

		return static::$em;
	}

	public function testBuilder()
	{
		$reader = new IndexedReader(new AnnotationReader());
		$qb = new QueryBuilder(
			self::getEntityManager(),
			$reader,
			__NAMESPACE__ . '\\User'
		);

		$query = $qb->createDoctrineQueryBuilder('test me');
		$this->assertEquals(
			'SELECT entity FROM Ost\Doctrine\MultiColumnSearch\User entity WHERE entity.id IN(SELECT c.id FROM Ost\Doctrine\MultiColumnSearch\User c WHERE (c.name LIKE ?2 OR c.email LIKE ?2) AND (c.id IN(SELECT b.id FROM Ost\Doctrine\MultiColumnSearch\User b WHERE b.name LIKE ?1 OR b.email LIKE ?1)))',
			$query->getDQL()
		);
		$this->assertEquals(array(1 => 'test', 2 => 'me'), $query->getParameters());

		$query = $qb->createDoctrineQueryBuilder('te*t me');
		$this->assertEquals(array(1 => 'te%t', 2 => 'me'), $query->getParameters());

		$query = $qb->createDoctrineQueryBuilder('*test*');
		$this->assertEquals(array(1 => '%test%'), $query->getParameters());
	}
}
