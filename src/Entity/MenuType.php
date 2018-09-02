<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu_type")
 * @ORM\Entity(repositoryClass="App\Repository\MenuTypeRepository")
 */
class MenuType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="menuTypeId")
     */
    private $typeId;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return BlogPost
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

public function __toString()
{
    return $this->getTitle();
}

}
