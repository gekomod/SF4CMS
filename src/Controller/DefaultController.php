<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\BlogPost;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        
        $retofit = $this->getDoctrine()
        ->getRepository(BlogPost::class)
        ->findBy(['category' => 3]);
        
        if (!$retofit) {
            $error = 'Brak Wpisów';
            $products = '';
        } else {
            $error = '';
            $products = $retofit;
            
        }
        
	return $this->render('home/index.html.twig', ['info' => $products, 'error' => $error]);
    }

        /**
     * @Route("/test", name="test")
     */
public function tes() {
$retofit = $this->getDoctrine()
        ->getRepository(BlogPost::class)
        ->findBy(['category' => 3]);
        
        if (!$retofit) {
            $error = 'Brak Wpisów';
            $products = '';
        } else {
            $error = '';
            $products = $retofit;
            
        }
        
	return $this->render('home/index.html.twig', ['info' => $products, 'error' => $error]);

}

/**
     * @Route("/faq/{slug}", name="faq_page")
     */
    public function readfaq($slug)
    {

      $product = $this->getDoctrine()
        ->getRepository(BlogPost::class)
        ->findOneBy(['slug' => $slug, 'category' => 3]);

      $seoPage = $this->container->get('sonata.seo.page');
        $seoPage->setTitle($product->title);

      if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.$id
        );
      }

     return $this->render('home/page.html.twig', ['wpis' => $product]);
  }


}
