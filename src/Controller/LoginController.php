<?php

namespace App\Controller;

use function GuzzleHttp\Promise\exception_for;
use http\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {

    $authenticationUtils = $this->get('security.authentication_utils');

    // pobranie błędu logowania, jeśli sie taki pojawił
    $error = $authenticationUtils->getLastAuthenticationError();

    // nazwa użytkownika ostatnio wprowadzona przez aktualnego użytkownika
    $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
	    'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
      * @Route("/login_check", name="login_check")
      */
     public function loginCheckAction()
     {
         // ta akcja nie będzie wykonywana,
         // ponieważ trasa jest wykorzystywana przez system bezpieczeństwa
     }

    /**
     * @Route("/login/check-facebook", name="facebook_login")
     */
    public function loginFacebookAction()
    {
     throw new \Exception('Już wkrótce Będzie Logowanie z FB/Github');
    }

    /**
      * @Route("/forgot", name="forgot")
      */
     public function ForgotAction()
     {
        return $this->render('login/forgot.html.twig', [
            'controller_name' => 'ForgotController',
        ]);
     }


}
