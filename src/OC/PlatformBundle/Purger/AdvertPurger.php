<?php

namespace OC\PlatformBundle\Purger;

use OC\PlatformBundle\Entity\Advert;

use Doctrine\ORM\EntityManagerInterface;

class AdvertPurger
{
	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	public function purge($days)
	{
		$date = new \Datetime($days. 'days ago'); //On récupère la date d'il y a $days jours

		$listAdverts = $this->em->getRepository('OCPlatformBundle:Advert')->getAdvertsBefore($date);

		foreach ($listAdverts as $advert) {
			$listAdvertSkills = $this->em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));

			// Récupération de l'image associé à l'advert pour pouvoir la supprimer
			// et avoir une base de données clean
			$associatedImage = $advert->getImage();

			foreach ($listAdvertSkills as $advertSkill) {
				$this->em->remove($advertSkill);
			}

			$this->em->remove($associatedImage);
			$this->em->remove($advert);
		}

		$this->em->flush();
	}
}

?>