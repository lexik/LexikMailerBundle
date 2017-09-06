<?php

namespace Lexik\Bundle\MailerBundle\Tools;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class SimplePager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $itemPerPage;

    /**
     * @var array
     */
    private $results;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $maxPage;

    /**
     * @param EntityManager $em
     * @param int           $itemPerPage
     */
    public function __construct(EntityManager $em, $itemPerPage)
    {
        $this->em = $em;
        $this->itemPerPage = $itemPerPage;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * @param string $entityName
     *
     * @return QueryBuilder
     */
    protected function createQueryBuilder($entityName)
    {
        return $this->em
            ->getRepository($entityName)
            ->createQueryBuilder('r')
            ->orderBy('r.reference')
        ;
    }

    /**
     * @param string $entityName
     * @param int    $page
     *
     * @return \Lexik\Bundle\MailerBundle\Tools\SimplePager
     */
    public function retrievePageElements($entityName, $page)
    {
        $page = $page < 1 ? 1 : $page;

        $qb = $this->createQueryBuilder($entityName);
        $qb
            ->setMaxResults($this->itemPerPage)
            ->setFirstResult(($page - 1) * $this->itemPerPage)
        ;

        $paginator = new Paginator($qb, true);

        $this->page = $page;
        $this->count = $paginator->count();
        $this->results = $paginator->getIterator();
        $this->maxPage = ceil($this->count / $this->itemPerPage);

        return $this;
    }
}
