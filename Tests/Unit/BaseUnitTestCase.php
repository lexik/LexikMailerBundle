<?php

namespace Lexik\Bundle\MailerBundle\Tests\Unit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Annotations\AnnotationReader;

use Lexik\Bundle\MailerBundle\Tests\Fixtures\TestData;

/**
 * Base unit test class providing functions to create a mock entity manger, load schema and fixtures.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
abstract class BaseUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Create the database schema.
     *
     * @param EntityManager $om
     */
    protected function createSchema(EntityManager $em)
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Load test fixtures.
     *
     * @param EntityManager $om
     */
    protected function loadFixtures(EntityManager $em)
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $fixtures = new TestData();
        $executor->execute(array($fixtures), false);
    }

    /**
     * EntityManager mock object together with annotation mapping driver and
     * pdo_sqlite database in memory
     *
     * @return EntityManager
     */
    protected function getMockSqliteEntityManager()
    {
        $cache = new \Doctrine\Common\Cache\ArrayCache();

        // annotation driver
        $reader = new AnnotationReader($cache);
        $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array(
            __DIR__.'/../../Entity',
        ));

        // configuration mock
        $config = $this->getMock('Doctrine\ORM\Configuration');
        $config->expects($this->any())
            ->method('getMetadataCacheImpl')
            ->will($this->returnValue($cache));
        $config->expects($this->any())
            ->method('getQueryCacheImpl')
            ->will($this->returnValue($cache));
        $config->expects($this->once())
            ->method('getProxyDir')
            ->will($this->returnValue(sys_get_temp_dir()));
        $config->expects($this->once())
            ->method('getProxyNamespace')
            ->will($this->returnValue('Proxy'));
        $config->expects($this->once())
            ->method('getAutoGenerateProxyClasses')
            ->will($this->returnValue(true));
        $config->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($annotationDriver));
        $config->expects($this->any())
            ->method('getClassMetadataFactoryName')
            ->will($this->returnValue('Doctrine\ORM\Mapping\ClassMetadataFactory'));
        $config->expects($this->any())
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue('Doctrine\\ORM\\EntityRepository'));
        $config->expects($this->any())
            ->method('getQuoteStrategy')
            ->will($this->returnValue(new DefaultQuoteStrategy()));

        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $em = \Doctrine\ORM\EntityManager::create($conn, $config);

        return $em;
    }
}