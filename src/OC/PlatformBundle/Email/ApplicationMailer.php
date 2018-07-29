<?php

namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;

class ApplicationMailer
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	public function __construct(\Swift_Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendNewNotification(Application $application)
	{
		$message = new \Swift_Message('Nouvelle Candidature', 'Vous avez reçu une nouvelle candidature');

		$message->addTo('defevera@gmail.com')->addFrom('admin@jobs.com');

		$this->mailer->send($message);
	}
}

?>