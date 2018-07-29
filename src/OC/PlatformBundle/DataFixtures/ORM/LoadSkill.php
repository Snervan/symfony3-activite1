<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadSkill implements FixtureInterface
{
	public function load(ObjectManager $manager) 
	{
		//Liste des compétences à ajouter
		$names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-notes');

		foreach($names as $name) {
			$skill = new Skill();
			$skill->setName($name);

			//On la persiste
			$manager->persist($skill);
		}

		//On déclenche l'enregistrement
		$manager->flush();
	}
}

?>