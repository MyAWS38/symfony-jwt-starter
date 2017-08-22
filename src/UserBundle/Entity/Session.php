<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sessions")
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\Column(name="sess_id", type="string", length=64, nullable=false)
     */
    protected $id;

    /**
     * @ORM\Column(type="blob")
     */
    protected $sessData;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sessTime;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sessLifetime;

    public function getId()
    {
        return $this->id;
    }

    public function getSessData()
    {
        return $this->sessData;
    }

    public function setSessData($sessData)
    {
        $this->sessData = $sessData;
    }

    public function getSessTime()
    {
        return $this->sessTime;
    }

    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;
    }

    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }

    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;
    }
}
