<?php

namespace App\Controller;

use GuzzleHttp\Client;
use function GuzzleHttp\Promise\exception_for;
use http\Exception;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Request as Req;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginController extends Controller
{

    protected $github_url = 'https://github.com/login/oauth/access_token';

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
            'fburl' => $this->loginFacebook(),
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

    public function loginFacebook()
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
        $permissions = ['email','public_profile']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('https://webpi.pl/login/check-facebook', $permissions);

        return $loginUrl;
    }

    /**
     * @Route("/login/check-facebook", name="facebook_login")
     */
    public function loginFacebookAction()
    {
        $request = Request::createFromGlobals();
        $session = new Session();
        $session->clear();

        $facebook_id = $this->container->get('settings.new')->getSettings('facebook_id');
        $facebook_secret = $this->container->get('settings.new')->getSettings('facebook_client');
        $fb = new \Facebook\Facebook([
            'app_id' => $facebook_id,
            'app_secret' => $facebook_secret,
            'default_graph_version' => 'v2.10',
            'enable_beta_mode' => false,
        ]);

        $helper = $fb->getRedirectLoginHelper();
        if (null !== $request->query->get('state')) { $helper->getPersistentDataHandler()->set('state', $request->query->get('state')); }

        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        $oAuth2Client = $fb->getOAuth2Client();

        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        $tokenMetadata->validateAppId($facebook_id);
        $tokenMetadata->validateExpiration();

        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,name,email,first_name,last_name,about,website,name_format,short_name,address,age_range', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();

        $succesfullyRegistered = $this->register($user->getProperty('email'),\str_replace(" ","",$user->getProperty('name')),\str_replace(" ","",$user->getProperty('name')),$user->getProperty('id'),null);

        if($succesfullyRegistered){
            echo 'REJESTRACJA POWIODŁA SIĘ';
            echo 'Name: ' . \str_replace(" ","",$user->getProperty('name'));
        }else{
            echo 'REJESTRACJA NIE POWIODŁA SIĘ';
            echo 'email: ' . $user->getProperty('email');
            echo 'id: ' . $user->getProperty('id');
            echo 'gender: ' . $user->getProperty('gender');
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;
        $em = $this->getDoctrine()->getManager();
        //$usersRepository = $em->getRepository("mybundleuserBundle:User");
        // or use directly the namespace and the name of the class
        $usersRepository = $em->getRepository("App\Application\Sonata\UserBundle\Entity\User");
        $checklogins = $usersRepository->findOneBy(array('email' => $user->getProperty('email')));

        try {
            $token = new UsernamePasswordToken($checklogins, null, 'main', $checklogins->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));
            $this->addFlash('success', 'Logowanie zakonczone poprawnie');
            return $this->redirectToRoute('fos_user_profile_show');
        } catch (\Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
            return $this->redirectToRoute('login');
        }

    }

    /**
     * This method registers an user in the database manually.
     *
     * @return boolean User registered / not registered
     **/
    private function register($email,$username,$password,$id,$uid){
        $userManager = $this->get('fos_user.user_manager');

        $email_exist = $userManager->findUserByEmail($email);

        // Check if the user exists to prevent Integrity constraint violation error in the insertion
        if($email_exist){
            //$update = $userManager->createUser();
            $email_exist->setEmail($email);
            if($id == null) {
                $email_exist->setTwitterUid($uid);
            } else {
                $email_exist->setfacebookUid($id);
            }
            $userManager->updateUser($email_exist);
            return false;
        }

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
        // this method will encrypt the password with the default settings :)
        if($id == null) {
            $user->setTwitterUid($uid);
        } else {
            $user->setfacebookUid($id);
        }
        $user->setPlainPassword($password);
        $userManager->updateUser($user);

        return true;
    }

    /**
     * @Route("/login/check-google", name="google_login")
     */
    public function loginGoogleAction()
    {
        $secret = $this->container->get('settings.new')->getSettings('google_secret');
        $id = $this->container->get('settings.new')->getSettings('google_id');
        $request = Request::createFromGlobals();
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://accounts.google.com',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        if(null !== $request->query->get('code')) {
            $check = $client->post('/o/oauth2/token',['query' => ['code'=>$request->query->get('code'),
                'client_id' => $id,
                'client_secret'=>$secret,
                'redirect_uri' => 'https://webpi.pl/login/check-google',
                'grant_type' => 'authorization_code']]);

            var_dump($check->getBody()->getContents());
        } else {
            $url = "https://accounts.google.com/o/oauth2/v2/auth?scope=profile&access_type=offline&include_granted_scopes=true&state=state_parameter_passthrough_value&redirect_uri=https://webpi.pl/login/check-google&response_type=code&client_id=$id";
            return $this->redirect($url);
        }


        throw new \Exception($secret);
    }

    /**
     * @Route("/login/check-github", name="github_login")
     */
    public function loginGithubAction()
    {
        $secret = $this->container->get('settings.new')->getSettings('github_secret');
        $id = $this->container->get('settings.new')->getSettings('github_id');
        $request = Request::createFromGlobals();
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.github.com',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        if(null !== $request->query->get('code')) {
            $postdata = \http_build_query(
                array(
                    'client_id' => $id,
                    'client_secret' => $secret,
                    'scope' => 'user,user:email',
                    'code' => $request->query->get('code')
                )
            );
            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );

            $context = \stream_context_create($opts);
            $result = \file_get_contents($this->github_url, false, $context);
            $json_url = 'https://api.github.com/user?'.$result;
            $ac = explode('&',$result);
            $access = str_replace("access_token=","", $ac[0]);

            $responses = $client->get('/user/emails?access_token='.$access, ['verify' => true]);

            $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
            $context  = \stream_context_create($options);
            $response = \file_get_contents($json_url, false, $context);
            $response = \json_decode($response);
            $response->email = \json_decode($responses->getBody()->getContents())[0]->email;

            try {
                $this->register($response->email, $response->login, $response->login, null, $response->id);
            } catch (\Exception $error){
                echo $error->getMessage();
            }

            $em = $this->getDoctrine()->getManager();
            $usersRepository = $em->getRepository("App\Application\Sonata\UserBundle\Entity\User");
            $checklogins = $usersRepository->findOneBy(array('email' => $response->email));

            try {
                if(null !== $checklogins->getRoles() ) {
                    $token = new UsernamePasswordToken($checklogins, null, 'main', $checklogins->getRoles());
                    $this->container->get('security.token_storage')->setToken($token);
                    $this->container->get('session')->set('_security_main', serialize($token));
                    $this->addFlash('success', 'Logowanie zakonczone poprawnie');
                    return $this->redirectToRoute('fos_user_profile_show');
                }
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
                return $this->redirectToRoute('login');
            }

        } else {
            $url = "https://github.com/login/oauth/authorize?client_id=$id&scope=user,user:email";
            return $this->redirect($url);
        }
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
