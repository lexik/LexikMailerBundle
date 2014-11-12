<?php

namespace Lexik\Bundle\MailerBundle\Tools;

use Doctrine\ORM\EntityManager;
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
     * @var integer
     */
    private $itemPerPage;

    /**
     * @var array
     */
    private $results;

    /**
     * @var integer
     */
    private $count;

    /**
     * @var integer
     */
    private $page;

    /**
     * @var integer
     */
    private $maxPage;

    /**
     * @param EntityManager $em
     * @param integer       $itemPerPage
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
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return integer
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * @param string  $entityName
     * @param integer $page
     * @return \Lexik\Bundle\MailerBundle\Tools\SimplePager
     */
    public function retrievePageElements($entityName, $page)
    {
        $page = $page < 1 ? 1 : $page;

        $qb = $this->em
            ->getRepository($entityName)
            ->createQueryBuilder('r')
            ->orderBy('r.reference')
            ->setMaxResults($this->itemPerPage)
            ->setFirstResult(($page-1) * $this->itemPerPage)
        ;

        $paginator = new Paginator($qb, true);

        $this->page = $page;
        $this->count = $paginator->count();
        $this->results = $paginator->getIterator();
        $this->maxPage = ceil($this->count / $this->itemPerPage);

        return $this;
    }
}
