<?php
namespace common\domain;

/**
 *
 * Abstract class for domain events. Every domain event class should inherit from this class
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
abstract class AbstractDomainEvent implements \JsonSerializable {
	/**
	 * The event identifier
	 *
	 * @var int
	 * @access protected
	 */
	protected $eventId;
	
	/**
	 * The timestamp when the event occured. Should also include microseconds for accuracy.
	 * Microseconds also helps with deduplication
	 *
	 * @var \DateTime
	 * @access protected
	 */
	protected $occuredOn;

	/**
	 * Object initializer
	 *
	 * @return void
	 * @throws --
	 *
	 * @access protected
	 * @since Method/function available since Release 1.0
	 */
	protected function __construct() {
		$this->occuredOn = \DateTime::createFromFormat(
				'U.u', time() . '.' . intval((explode(' ', microtime())[0]) * 1000000));
	}

	/**
	 * @return int
	 */
	public function getEventId() {
		return $this->eventId;
	}
	
	/**
	 * 
	 * @return \DateTime
	 */
	public function getOccuredOn() {
		return $this->occuredOn;
	}
	
	/**
	 * @param string $prefix The prefix that should be prepended to the event type
	 * 
	 * @return string
	 * @throws \DomainException If the domain event class is an anonymous type of class
	 */
	public function getEventType($prefix = NULL) {
		if((new \ReflectionClass($this))->isAnonymous()) {
			throw new \DomainException('Anounymous classes should not use the default getEventType'
					. ' method. They should have their own getEvetType implementation.');
		}
		
		return $prefix . '.' . str_replace('\\', '.', get_class($this));
	}
	
	/**
	 * Helps into serializing the object in json format. Datetime is returned as timestamp in
	 * microseconds format (timestamp.microseconds)
	 *
	 * @return array
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function jsonSerialize() {
		return $this->getArrayFromObject($this);
	}
	
	final protected function getArrayFromObject($object) {
		$resArray = array();
		
		$reflectionClass = new \ReflectionClass($object);
		$properties = $reflectionClass->getProperties(
				\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED 
				| \ReflectionProperty::IS_PRIVATE);
		
		foreach($properties as $prop) {
			if($prop->isPrivate() || $prop->isProtected()) {
				$prop->setAccessible(TRUE);
			}
			
			$value = $prop->getValue($object);

			if(is_object($value)) {
				if($value instanceof \DateTime) {
					$value = $value->format('U.u');
				}
				else {
					$value = $this->getArrayFromObject($value);

				}
			}

			$resArray[$prop->getName()] = $value;
		}
		
		return $resArray;
	}
}

?>
