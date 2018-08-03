<?php

namespace OC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
    	//Si le visiteur est déjà identifié, on le redirige vers l'accueil
    	if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
    		return $this->redirectToRoute('oc_platform_homepage');
    	}

    	//Le service authication_utils permet de récupérer le nom d'utilisateur
    	$authenticationUtils = $this->get('security.authentication_utils');

    	return $this->render('OCUserBundle:Security:login.html.twig', array(
    		'last_username' => $authenticationUtils->getLastUsername(),
    		'error' => $authenticationUtils-> getLastAuthenticationError()));
    }
}
