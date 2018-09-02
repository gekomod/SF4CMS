<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    public $name;

    /**
    * @ORM\OneToMany(targetEntity="BlogPost", mappedBy="category")
    */
    public $blogPosts;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
    }

    public function getBlogPosts()
    {
        return $this->blogPosts;
    }


}
