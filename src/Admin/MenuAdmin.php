<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use App\Entity\MenuType;
use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MenuAdmin extends AbstractAdmin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array())
            ->add('icon', null, array('required' => false))
            ->add('route', null, array())
            ->add('alias', null, array())
            ->add('static', null, array('required' => false))
            ->add('menuTypeId', EntityType::class, array(
                    'class'=>MenuType::class,
                    'choice_label'=>'title',
                    'required' => false
                )
            )
            ->add('parent', EntityType::class, array('class'=>Menu::class,'required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array())
            ->add('id', null, array())
            ->add('route', null, array())
        ;
    }

    public function configureShowField(ShowMapper $showMapper){
        $showMapper
            ->add('title', null, array())
            ->add('id', null, array())
->add('parent', null, array())
            ->add('route', null, array())
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('route', null, array())
            ->add('id', null, array())
            ->add('menuTypeId', EntityType::class, array(
                    'class'=> MenuType::class,
                    'property'=>'title'
                )
            )
	    ->add('parent')            
        ;
    }
}
