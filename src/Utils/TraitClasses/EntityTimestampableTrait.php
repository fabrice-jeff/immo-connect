<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN Rogelio
 * Date: 02/01/2020
 * Time: 09:31
 */

namespace App\Utils\TraitClasses;


use Doctrine\ORM\Mapping as ORM;

trait EntityTimestampableTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private $deleted;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deletedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->deleted = false;
        $this->deletedAt = null;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUpdatedAt()
    {
        return !!$this->updatedAt;
    }
    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param    \DateTime $updatedAt
     */
    public function setUpdatedAt( $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param    \DateTime $deletedAt
     */
    public function setDeletedAt( $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
}
