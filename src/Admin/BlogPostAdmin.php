<?php

// src/Admin/BlogPostAdmin.php
namespace App\Admin;

use App\Entity\Category;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

// ...
class BlogPostAdmin extends AbstractAdmin
{
    
protected function configureFormFields(FormMapper $formMapper)
{
    $formMapper
        ->add('title', TextType::class)
        ->add('body', TextareaType::class)
        ->add('description', TextareaType::class,['label' => 'Opis'])

        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
        ])
    ;
}

// ...

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('category.name')
            ->add('slug',TextType::class, ['editable' => 'true'])
            ->add('draft',null, ['editable' => 'true'])
            ->add('updatedat','date',array(
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('category', null, [], EntityType::class, [
                'class'    => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

}
