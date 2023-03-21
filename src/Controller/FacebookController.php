<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FacebookRegistrationType;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\FacebookUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\FacebookAuthenticator;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class FacebookController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/facebook", name="connect_facebook_start")
     * @param ClientRegistry $clientRegistry
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
public function connectAction(ClientRegistry $clientRegistry)
{
// on Symfony 3.3 or lower, $clientRegistry = $this->get('knpu.oauth2.registry');

// will redirect to Facebook!
return $clientRegistry
->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
->redirect([
'public_profile', 'email' // the scopes you want to access
])
;
}

/**
* After going to Facebook, you're redirected back here
* because this is the "redirect_route" you configured
* in config/packages/knpu_oauth2_client.yaml
*
* @Route("/connect/facebook/check", name="connect_facebook_check")
*/
public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
{
// ** if you want to *authenticate* the user, then
// leave this method blank and create a Guard authenticator
// (read below)

/** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
$client = $clientRegistry->getClient('facebook_main');

try {
// the exact class depends on which provider you're using
/** @var \League\OAuth2\Client\Provider\FacebookUser $user */
$user = $client->fetchUser();

// do something with all this new power!
// e.g. $name = $user->getFirstName();
var_dump($user); die;
// ...
} catch (IdentityProviderException $e) {
// something went wrong!
// probably you should return the reason to the user
var_dump($e->getMessage()); die;
}
}

    /**
     * @Route("/connect/facebook/registration", name="connect_facebook_registration")
     * @param Request $request
     * @param FacebookAuthenticator $facebookAuthenticator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $GuardAuthenticatorHandler
     * @return Response
     */
    public function finishRegistration(Request $request, FacebookAuthenticator $facebookAuthenticator, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $GuardAuthenticatorHandler)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $facebookAuthenticator
            ->getUserInfoFromSession($request);
        if (!$facebookUser) {
            throw $this->createNotFoundException('How did you get here without user information!?');
        }
        $user = new User();
        $user->setFacebookId($facebookUser->getId());
        $user->setEmail($facebookUser->getEmail());

        $form = $this->createForm(FacebookRegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if ($form->isValid()) {


                // encode the password manually
                $plainPassword = $form['plainPassword']->getData();
                $encodedPassword = $passwordEncoder
                ->encodePassword($user, $plainPassword);
                $user->setPassword($encodedPassword);

                $user->setRoles(['ROLE_NIEANCZEJ']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                // remove the session information
                $request->getSession()->remove('facebook_user');
                // log the user in manually
                $guardHandler = $GuardAuthenticatorHandler;
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $facebookAuthenticator,
                    'main' // the firewall key
                );
            }
        }
        return $this->render('register/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }



}