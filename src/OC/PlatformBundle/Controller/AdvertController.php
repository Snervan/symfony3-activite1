<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AdvertController extends Controller
{
	public function indexAction($page)
	{
		if($page < 1) {
			throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
		}

		$repository = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert');

		$nbPerPage = 3;

		$listAdverts = $repository->getAdverts($page, $nbPerPage);

		$nbPages = ceil(count($listAdverts) / $nbPerPage);

		if($page > $nbPages) {
			throw new NotFoundHttpException('Page "'.$page.'" inexistante');
		}

		if(null === $listAdverts) {
			echo "Aucune donnée dans la BDD";
		}

		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listAdverts,
			'nbPages' => $nbPages,
			'page' => $page
		));
	}

	public function menuAction($limit)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert');

		$listAdverts = $repository->findBy(array(), array('date' => 'DESC'), $limit, 0);

		if(null === $listAdverts) {
			echo "Aucune donnée dans la BDD";
		}

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			'listAdverts' => $listAdverts));
	}

	public function viewAction($id)
	{
		$em = $this->getDoctrine()->getManager();


		//On récupère l'entité correspondant à l'id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		//On récupère la liste des candidatures
		$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert));

		//On récupère la liste des advertSkill
		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
								->findBy(array('advert' => $advert));


		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert,
			'listApplications' => $listApplications,
			'listAdvertSkills' => $listAdvertSkills));
	}

	public function addAction(Request $request)
	{
    	// On récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();

		if($request->isMethod('POST'))
		{
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}

		return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
	}

	public function editAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		//On récupère l'annonce id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
		}

		if($request->isMethod('POST')) {
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}

	    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
	      'advert' => $advert
	    ));
	}

	public function deleteAction($id) 
	{
		$em = $this->getDoctrine()->getManager();

		//On récupère l'annonce id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		//On boucle
		foreach($advert->getCategories() as $category) {
			$advert->removeCategory($category);
		}

		$em->flush();

		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert));
	}

	public function purgeAction($days, Request $request)
	{
		$purger = $this->get('oc_platform.purger.advert');

		$purger->purge($days);

		$request->getSession()->getFlashBag()->add('info', 'les annonces vieilles de '. $days .' jour(s) et plus sans candidatures ont été supprimées');

		return $this->redirectToRoute('oc_core_homepage');
	}
}