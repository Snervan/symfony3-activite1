<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction()
    {
    	//La liste d'annonces qu'on doit afficher dans l'index du CoreBundle
    	$listAdverts = array(
	      array(
	        'title'   => 'Recherche développpeur Symfony3',
	        'id'      => 1,
	        'author'  => 'Alexandre',
	        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Mission de webmaster',
	        'id'      => 2,
	        'author'  => 'Hugo',
	        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
	        'date'    => new \Datetime()),
	      array(
	        'title'   => 'Offre de stage webdesigner',
	        'id'      => 3,
	        'author'  => 'Mathieu',
	        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
	        'date'    => new \Datetime())
	    );

        return $this->render('OCCoreBundle:Core:index.html.twig', array(
        	'listAdverts' => $listAdverts));
    }

    public function contactAction(Request $request)
    {
    	$messageFlash = $request->getSession();

    	//Ajoute un message flash qui sera affiché à l'accueil après redirection
    	$messageFlash->getFlashBag()->add('info', "La page contact n'est pas encore disponible, merci de revenir plus tard.");

    	return $this->redirectToRoute('oc_core_homepage');
    }
}
