<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadCategory.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;

class LoadAdverts implements FixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    $defaultImage = new Image();
    $defaultImage->setUrl('http://www.zen-et-efficace.com/wp-content/uploads/JobDeReve.jpg');
    $defaultImage->setAlt('Image job de rêve');

    $defaultImage2 = new Image();
    $defaultImage2->setUrl('https://upload.wikimedia.org/wikipedia/fr/thumb/2/2e/Java_Logo.svg/1200px-Java_Logo.svg.png');
    $defaultImage2->setAlt('Logo JAVA');

    $defaultImage3 = new Image();
    $defaultImage3->setUrl('https://s-i.huffpost.com/gen/2130764/images/o-TECHNOLOGY-WEB-facebook.jpg');
    $defaultImage3->setAlt('Image job dév web junior');

    $advert1 = new myAdverts();
    $advert1->title = 'Recherche développeur Symfony3';
    $advert1->content = 'Cherche développeur Symfony à Lyon';
    $advert1->author = 'Job Advertising Company';
    $advert1->image = $defaultImage;
    $advert1->date = new \Datetime();

    $advert2 = new myAdverts();
    $advert2->title = 'Recherche développeur JAVA';
    $advert2->content = 'Cherche développeur JAVA à Paris';
    $advert2->author = 'Quick Job';
    $advert2->image = $defaultImage2;
    $advert2->date = new \Datetime('55 days ago');

    $advert3 = new myAdverts();
    $advert3->title = 'Recherche développeur web junior';
    $advert3->content = 'Cherche développeur web junior maîtrisant PHP à Paris';
    $advert3->author = 'Quick Job';
    $advert3->image = $defaultImage3;
    $advert3->date = new \Datetime('10 days ago');

    $listAdverts = array($advert1, $advert2, $advert3);

    foreach ($listAdverts as $advert) {
      $newAdvert = new Advert();
      $newAdvert->setTitle($advert->title);
      $newAdvert->setContent($advert->content);
      $newAdvert->setAuthor($advert->author);
      $newAdvert->setDate($advert->date);
      $newAdvert->setImage($advert->image);

      // On la persiste
      $manager->persist($newAdvert);
    }

    // On déclenche l'enregistrement de toutes les annonces
    $manager->flush();
  }
}

class myAdverts
{
  public $title;
  public $author;
  public $content;
  public $image;
  public $date;
}



