<?php
namespace common\domain;

use common\application\IDomainEventHandler;

/**
 *
 * This class is used to listen and register domain events. Also it will map the events 
 * to registered listeners and publish/handle them . Singleton class
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
class DomainEventPublisher {
	/**
	 * The event subscribers array. An array of event handlers
	 *
	 * @var IDomainEventHandler[]
	 * @access private
	 */
	private $subscribers = array();
	
	/**
	 * The collection of all published events
	 *
	 * @var AbstractDomainEvent[]
	 * @access private
	 */
	private $publishedEvents = array();
	
	/**
	 * Singleton object instance
	 *
	 * @var DomainEventPublisher
	 * @access private
	 */
	private static $instance = NULL;
	
	/**
	 * If is publishing or not
	 *
	 * @var bool
	 * @access private
	 */
	private $publishing = FALSE;
	
	/**
	 * Object initializer set to private to act as a singleton
	 *
	 * @return void
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	private function __construct() {
	}
	
	/**
	 * Get the singleton instance
	 *
	 * @return DomainEventPublisher
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function getInstance() {
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Resets the publisher. Cleans all the registered subscribers
	 *
	 * @return void
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function reset() {
		$this->subscribers = array();
		$this->publishedEvents = array();
	}
	
	/**
	 * Publish an event
	 * 
	 * @param AbstractDomainEvent $event The domain event to be published
	 *
	 * @return void
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function publish(AbstractDomainEvent $event) {
		$this->publishedEvents[] = $event;
	}
	
	/**
	 * Runs all the events through subscribers
	 * 
	 * @return void
	 * @throws \Exception
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function runEventsThroughSubscribers() {		
		if($this->isPublishing()) {
			return;
		}
		
		$this->setPublishing(TRUE);
		
		foreach($this->publishedEvents as $event) {
			$publishedEventType = get_class($event);
			foreach($this->subscribers as $eventHandler) {
				$typeOfEventSubscribedTo = $eventHandler->subscribedToEventType();
				if($typeOfEventSubscribedTo === $publishedEventType
						|| $typeOfEventSubscribedTo === AbstractDomainEvent::class) {
					$eventHandler->handle($event);
				}
			}
		}
		
		$this->publishedEvents = array();
		$this->setPublishing(FALSE);
	}
	
	/**
	 * Subscribe an event handler to handle the domain event that might be published
	 * 
	 * @param IDomainEventHandler $domainEventHandler The domain event handler to handle the 
	 * published event
	 *
	 * @return void
	 * @throws \InvalidArgumentException If the domain event handler has an improper signiature in its
	 * handle method
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function subscribe(IDomainEventHandler $domainEventHandler) {		
		$this->subscribers[] = $domainEventHandler;
	}
	
	private function isPublishing() {
		return $this->publishing;
	}
	
	private function setPublishing($publishing) {
		$this->publishing = $publishing;
	}
	
	/**
	 * 
	 * @return IDomainEventHandler[]
	 */
	public function getSubscribers(): array {
		return $this->subscribers;
	}

	/**
	 * 
	 * @return AbstractDomainEvent[]
	 */
	public function getPublishedEvents(): array {
		return $this->publishedEvents;
	}
}

?>
