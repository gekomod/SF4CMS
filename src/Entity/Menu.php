<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="App\Repository\MenuRepository")
 */
class Menu
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $icon;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $route;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @ORM\Column(type="boolean")
     */
    private $static;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    /**
     * @var \App\Entity\Menu
     * @ORM\ManyToOne(targetEntity="App\Entity\Menu", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MenuType", inversedBy="menuTypeId")
     * @ORM\JoinColumn(name="menuTypeId", referencedColumnName="id")
     */
    public $menuTypeId;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getStatic(): ?bool
    {
        return $this->static;
    }

    public function setStatic(bool $static): self
    {
        $this->static = $static;

        return $this;
    }

    /**
     * Add children
     *
     * @param \App\Entity\Menu $children
     * @return Menu
     */
    public function addChild(\App\Entity\Menu $children)
    {
        $this->children[] = $children;
        return $this;
    }
    /**
     * Remove children
     *
     * @param \App\Entity\Menu $children
     */
    public function removeChild(\App\Entity\Menu $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

/**
     * Set parent
     *
     * @param \App\Entity\Menu $parent
     * @return Menu
     */
    public function setParent(\App\Entity\Menu $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }
    /**
     * Get parent
     *
     * @return \App\Entity\Menu 
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getMenuTypeId()
    {
        return $this->menuTypeId;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return Menu
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        
        return $this;
    }
    
    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    public function __toString() {
    return $this->title?:'';
}
}
