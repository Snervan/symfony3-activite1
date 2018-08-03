<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;

use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;

use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
			echo "Aucune données dans la BDD";
		}

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			'listAdverts' => $listAdverts));
	}

	/**
 	 * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
    */
	public function viewAction(Advert $advert)
	{
		$em = $this->getDoctrine()->getManager();

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

	/**
	 * @Security("has_role('ROLE_AUTEUR')")
	 */
	public function addAction(Request $request)
	{
		//On créé un objet Advert
		$advert = new Advert();

		//À partir du formBuilder, on génère le formulaire
		$form = $this->get('form.factory')->create(AdvertType::class, $advert);

		if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			//On créé l'évènement avec ses 2 arguments
			$event = new MessagePostEvent($advert->getContent(), $advert->getUser());

			//On déclenche l'évènement
			$this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

			//On récupère ce qui a été modifié par le ou les listeners, ici le message
			$advert->setContent($event->getMessage());

			$em = $this->getDoctrine()->getManager();
			$em->persist($advert);
			$em->flush();

			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}

		return $this->render('OCPlatformBundle:Advert:add.html.twig', array('form' => $form->createView()));
	}

	/**
 	 * @ParamConverter("advert", options={"mapping": {"id": "id"}})
    */
	public function editAction(Advert $advert, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
		}

		$form = $this->createForm(AdvertEditType::class, $advert);

		if($request->isMethod('POST')) {
			$form->handleRequest($request);

			if($form->isValid()) {
				$em->persist($advert);
				$em->flush();

				$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
				return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
			}
			
		}

	    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
	      'form' => $form->createView(),
	      'advert' => $advert
	    ));
	}

	public function deleteAction(Request $request, $id) 
	{
		$em = $this->getDoctrine()->getManager();

		//On récupère l'annonce id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		$form = $this->get('form.factory')->create();

		if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em->remove($advert);
			$em->flush();

			return $this->redirectToRoute('oc_platform_home');
		}

		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
			'advert' => $advert,
			'form' => $form->createView()
		));
	}

	public function purgeAction($days, Request $request)
	{
		$purger = $this->get('oc_platform.purger.advert');

		$purger->purge($days);

		$request->getSession()->getFlashBag()->add('info', 'les annonces vieilles de '. $days .' jour(s) et plus sans candidatures ont été supprimées');

		return $this->redirectToRoute('oc_core_homepage');
	}

	public function translationAction($name)
	{
		return $this->render('OCPlatformBundle:Advert:translation.html.twig', array(
			'name' => $name ));
	}
}