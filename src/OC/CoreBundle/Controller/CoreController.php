<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction()
    {
    	//La liste d'annonces qu'on doit afficher dans l'index du CoreBundle

        return $this->render('OCCoreBundle:Core:index.html.twig');
    }

    public function contactAction(Request $request)
    {
    	$messageFlash = $request->getSession();

    	//Ajoute un message flash qui sera affiché à l'accueil après redirection
    	$messageFlash->getFlashBag()->add('info', "La page contact n'est pas encore disponible, merci de revenir plus tard.");

    	return $this->redirectToRoute('oc_core_homepage');
    }
}
