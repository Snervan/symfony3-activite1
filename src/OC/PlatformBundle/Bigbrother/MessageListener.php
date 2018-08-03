<?php
namespace OC\PlatformBundle\Bigbrother;

use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Event\PlatformEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageListener implements EventSubscriberInterface
{
	protected $notificator;
	protected $listUsers = array();

	public function __construct(MessageNotificator $notificator)
	{
		$this->notificator = $notificator;
		$this->listUsers = $listUsers;
	}

	static public function getSubscribedEvents()
	{
		//On retourne un tableau "nom de l'event" => "méthode à éxécuter"
		return array(
			PlatformEvents::POST_MESSAGE => 'processMessage');
	}

	public function processMessage(MessagePostEvent $event)
	{
		//On active la surveillance si l'auteur du message est dans la liste
		if(in_array($event->getUser()->getUsername(), $this->listUsers)) {
			//On envoie un mail à l'admin
			$this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
		}
	}
}

?>