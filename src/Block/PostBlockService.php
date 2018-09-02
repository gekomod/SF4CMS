<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class NewsletterBlockService
 *
 * Renders a fake newsletter block for the sandbox
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class PostBlockService extends AbstractBlockService
{
protected $productAdmin;

    /**
     * @param string             $name
     * @param EngineInterface    $templating
     * @param ContainerInterface $container
     */
    public function __construct($name, EngineInterface $templating, ContainerInterface $container)
    {
        parent::__construct($name, $templating);

        $this->container =  $container;
        $this->manager   =  $this->container->get('doctrine')->getManager();
    }


    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $product = $blockContext->getBlock()->getSetting('productId');

        $parameters = array(
            'context'   => $blockContext,
            'settings'  => $settings,
            'block'     => $blockContext->getBlock(),
            'product'   => $product,
        );

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

   /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('title', 'text', array('required' => true)),
                array($this->getProductBuilder($formMapper), null, array()),
            )));
    }

/**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return \Symfony\Component\Form\FormBuilder
     */
    protected function getProductBuilder(FormMapper $formMapper)
    {

    }

    /**
     * @return mixed
     */
    public function getProductAdmin()
    {
        if (!$this->productAdmin) {
            $this->productAdmin = $this->container->get('sonata.block.service.post'); // cf. sonata services in .yml files
        }

        return $this->productAdmin;
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings.title')
            ->assertNotNull(array())
            ->assertNotBlank()
            ->assertMaxLength(array('limit' => 255))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'title'     => 'Blog',
            'mode'      => 'public',
            'product'   => false,
            'productId' => false,
            'template'  => 'ApplicationSonataPageBundle:Block:product.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $block->setSetting('productId', is_object($block->getSetting('productId')) ? $block->getSetting('productId')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $block->setSetting('productId', is_object($block->getSetting('productId')) ? $block->getSetting('productId')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        $product = $block->getSetting('productId');

        if ($product) {
            $product = $this->manager->getRepository('App\Entity\BlogPost')->findOneBy(array('id' => $product));
        }

        $block->setSetting('productId', $product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Blog Entity';
    }
}