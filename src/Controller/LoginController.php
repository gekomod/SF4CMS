<?php

namespace App\Controller;

use function GuzzleHttp\Promise\exception_for;
use http\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
#use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Google\Authenticator\GoogleAuthenticator;
use App\Application\SettingsBundle\Service\Settings_Get;

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
        $facebook_id = $this->container->get('settings.new')->getSettings('facebook_id');
        $facebook_secret = $this->container->get('settings.new')->getSettings('facebook_client');
        $fb = new \Facebook\Facebook([
            'app_id' => $facebook_id,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.10',
            'enable_beta_mode' => false,
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('https://webpi.pl/login/check-facebook', $permissions);

        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

     throw new \Exception('Już wkrótce Będzie Logowanie z FB/Github.');
    }

    /**
     * @Route("/login/check-google", name="google_login")
     */
    public function loginGoogleAction()
    {

        $secret = $this->container->get('settings.new')->getSettings('google_secret');
        throw new \Exception($secret);
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
