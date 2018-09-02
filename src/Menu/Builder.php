<?php
namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\Menu;
use Knp\Menu\ItemInterface;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $factory;
/**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct( FactoryInterface $factory,ContainerInterface $container)
    {
        $this->factory = $factory;
$this->container = $container;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function mainMenu(array $options)
    {
        $em1 = $this->container->get('doctrine')->getManager();
        $checker = $this->container->get('security.authorization_checker');
        $sC = $this->container->get('security.token_storage');
       // $menuItems = $this->container->get('menu')->getMainMenu();
	    $menuItems = $em1->getRepository(Menu::class)->findBy(array('menuTypeId'=>$options['id_kategori']));

        $menu = $this->factory->createItem('root',array('lastClass'=>false));
     //  $this->setCurrentItem($menu);

        $menu->setChildrenAttribute('class', 'dl-menu align-items-center');
        $menu->setExtra('currentClass', 'active');

        foreach($menuItems as $item) {
            if($item->getParent()==null) {
                if($item->getRoute() == 'null') {
                    $menu->addChild($item->getTitle(), array('uri' => $item->getAlias() ));
                } else {
                    $menu->addChild($item->getTitle(), array('route' => $item->getRoute()));
                }  
            }          

            if(count($menu->getChildren()) > 0) {
                $menu->setExtra('dropdown', true);
            }
        }

        if(count($menu->getChildren()) > 0) {
                $menu->addChild($item->getParent())->setLinkAttribute("uria",$item->getParent()->getAlias())->setAttribute('class', 'parent')->setChildrenAttribute('class', 'lg-submenu');
        }

//Generate SubMenu
   foreach($menuItems as $item) {
    if($item->getParent() == null) {} else {
        
        if($item->getRoute() == 'null') {
            $menu[$item->getParent()->getTitle()]->addChild($item->getTitle(), array('uri' => $item->getAlias()));
        } else {
            $menu[$item->getParent()->getTitle()]->addChild($item->getTitle(), array('route' => $item->getRoute()));
        }

    }
   }

//USER


      if ($checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        $menu->addChild('Profil', array('route' => 'fos_user_profile_show', 'label' => 'Witaj - '.ucfirst($sC->getToken()->getUser())))->setExtra('dropdown', true)->setAttribute('class', 'parent')->setChildrenAttribute('class', 'lg-submenu');
	    $menu['Profil']->addChild('Zmiana Hasla', array('route' => 'fos_user_change_password'));

      if ($checker->isGranted('ROLE_ADMIN')) {
        $menu['Profil']->addChild('Panel Administratora', array('route' => 'sonata_admin_dashboard'));
      }
        $menu['Profil']->addChild('Wyloguj', array('route' => 'fos_user_security_logout'))->setExtra('icon',"icon-lock");
      } else {
        $menu->addChild('Rejestracja', array('route' => 'fos_user_registration_register'))->setLinkAttribute('class', "btn btn-default nav-btn");
        $menu->addChild('Logowanie', array('route' => 'login'));
      }


        return $menu;
    }

    protected function setCurrentItem(ItemInterface $menu)
    {
        $menu->setCurrent($this->container->get('request_stack')->getCurrentRequest());
    }
}