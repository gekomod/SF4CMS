<?php

namespace App\Application\SettingsBundle\Block;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Doctrine\ORM\EntityManager;
use App\Application\Sonata\UserBundle\Entity\User;

class UsersBlockService extends AbstractBlockService
{
    private $manager;

    /**
     * @param string                    $name
     * @param EngineInterface           $templating
     * @param EntityManager             $manager
     *
     */
    public function __construct($name = null, EngineInterface $templating = null, $manager)
    {
        $this->manager = $manager;
        parent::__construct($name, $templating);
    }

    public function getName()
    {
        return 'Users Reader';
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'Zarejestrowanych Użytkowników',
            'url'      => 'admin_sonata_user_user_list',
            'template' => 'block/users.html.twig',
        ));
    }

    /**
     * The block context knows the default settings, but they can be
     * overwritten in the call to render the block.
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $settings = $blockContext->getSettings();

        $entityManager = $this->manager->getManager()->getRepository(User::class);
        $products = $entityManager->findAll();


        return $this->renderResponse($blockContext->getTemplate(), array(
            'content'     => count($products),
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }

    // These methods are required by the sonata block service interface.
    // They are not used in the CMF. To edit, create a symfony form or
    // a sonata admin.

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        throw new \Exception();
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        throw new \Exception();
    }
}