<?php

namespace App\Application\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SettingsAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array())
            ->add('var', null, array('required' => false))
            ->add('active', null, array())
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array())
            ->add('name', null, array())
            ->add('var', null, array())
        ;
    }

    public function configureShowField(ShowMapper $showMapper){
        $showMapper
            ->add('id', null, array())
            ->add('name', null, array())
->add('var', null, array())
            ->add('active', null, array())
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('var', null, [
            'editable' => true
        ])
            ->add('id', null, array())
	    ->add('active','boolean', [
            'editable' => true
        ])            
        ;
    }
}
