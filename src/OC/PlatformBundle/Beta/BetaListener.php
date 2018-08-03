<?php
namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
	// Notre processeur
	protected $betaHTML;

	// La date de fin de la version bêta :
	// - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
	// - Après cette date, on n'affichera plus la "bêta"
	protected $endDate;

	public function __construct(BetaHTMLAdder $betaHTML, $endDate)
	{
		$this->betaHTML = $betaHTML;
		$this->endDate = new \Datetime($endDate);
	}

	public function processBeta(FilterResponseEvent $event)
	{
		//On teste si la rêquête est bien la requête principale
		if(!$event->isMasterRequest()) {
			return;
		}

		$remainingDays = $this->endDate->diff(new \Datetime())->days;

		//Si la date est dépassée, on ne fait rien
		if($remainingDays <= 0) {
			return;
		}

		//On utilise notre betaHTML
		$response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);

		//On met à jour avec la nouvelle valeur
		$event->setResponse($response);
	}
}

?>