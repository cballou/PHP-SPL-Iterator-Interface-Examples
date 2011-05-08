<?php
/**
 * The EventDispatcher class provides a container for storing and dispatching
 * events. Modifications have been added to trigger specific methods (events)
 * by name as opposed to forcing the usage of update(). The singleton pattern
 * was also removed in addition to adding methods to override the default usage
 * of __call().
 *
 * Based on the original code:
 * http://forrst.com/posts/PHP_Event_handling-5Ke
 *
 * Ideas for scaling in the cloud:
 * http://www.slideshare.net/beberlei/towards-the-cloud-eventdriven-architectures-in-php
 *
 * @author 	Thomas RAMBAUD
 * @author	Corey Ballou
 * @version 1.1
 * @access 	public
 */
class EventDispatcher {

	// stores all created events
    private $_events = array(); 

	/**
	 * Default constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		// do nothing
	}

	/**
	 * Determine the total number of events.
	 *
	 * @access	public
	 * @return	int
	 */
    public function count()
	{
        return count($this->_events);
    }
        
	/**
	 * Add a new event by name.
	 *
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$triggersMethod
	 * @return	Event
	 */
    public function add($name, $triggersMethod = NULL)
	{
		if (!isset($this->_events[$name])) {
      		$this->_events[$name] = new Event($triggersMethod);
		}
        return $this->_events[$name];
    }

	/**
	 * Retrieve an event by name. If one does not exist, it will be created
	 * on the fly.
	 *
	 * @access	public
	 * @param	string	$name
	 * @return	Event
	 */
	public function get($name)
	{
		if (!isset($this->_events[$name])) {
			return $this->add($name);
		}
		return $this->_events[$name];
	}

	/**
	 * Retrieves all events.
	 *
	 * @access	public
	 * @return	array
	 */
    public function getAll()
	{
        return $this->_events;
    }

	/**
	 * Trigger an event. Returns the event for monitoring status.
	 *
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$data	The data to pass to the triggered event(s)
	 * @return	void
	 */
	public function trigger($name, $data)
	{
		$this->get($name)->notify($data);
	}

	/**
	 * Remove an event by name.
	 *
	 * @access	public
	 * @param	string	$name
	 * @return	bool
	 */
	public function remove($name)
	{
		if (isset($this->_events[$name])) {
			unset($this->_events[$name]);
			return true;
		}
		return false;
	}
    
	/**
	 * Retrieve the names of all current events.
	 *
	 * @access	public
	 * @return	array
	 */
    public function getNames()
	{
        return array_keys($this->_events);
    }                   
    
	/**
	 * Magic __get method for the lazy who don't wish to use the
	 * add() or get() methods. It will add an event if it doesn't exist,
	 * or simply return an existing event.
	 *
	 * @access	public
	 * @return	Event
	 */
    public function __get($name)
	{ 
        return $this->add($name);
    }
    
}

/**
 * Attach event handlers to an event to be notified
 * @author Thomas RAMBAUD
 * @version 1.0
 * @access public
 */
class Event implements SplSubject {

	// stores all attached observers
    private $_observers;
    
	/**
	 * Default constructor to initialize the observers.
	 *
	 * @access	public
	 * @return	void
	 */
    public function __construct()
	{
        $this->_observers = new SplObjectStorage();
    }
    
	/**
	 * Wrapper for the attach method, allowing for the addition
	 * of a method name to call within the observer.
	 *
	 * @access	public
	 * @param	SplObserver	$event
	 * @param	mixed		$triggersMethod
	 * @return	Event
	 */
	public function bind(SplObserver $event, $triggersMethod = NULL)
	{
		$this->_observers->attach($event, $triggersMethod);
		return $this;
	}
	
	/**
	 * Attach a new observer for the particular event.
	 *
	 * @access	public
	 * @param	SplObserver	$event
	 * @return	Event
	 */
    public function attach(SplObserver $event)
	{             
        $this->_observers->attach($event);      
        return $this;
    }

	/**
	 * Detach an existing observer from the particular event.
	 *
	 * @access	public
	 * @param	SplObserver	$event
	 * @return	Event
	 */
    public function detach(SplObserver $event)
	{           
        $this->_observers->detach($event);                
        return $this;
    }

	/**
	 * Notify all event observers that the event was triggered.
	 *
	 * @access	public
	 * @param	mixed	&$args
	 */
    public function notify(&$args = null)
	{
		$this->_observers->rewind();
		while ($this->_observers->valid()) {
			$triggersMethod = $this->_observers->getInfo();
			$observer = $this->_observers->current();
			$observer->update($this, $triggersMethod, $args);
			
			// on to the next observer for notification
			$this->_observers->next();
		}
    }
    
	/**
	 * Retrieves all observers.
	 *
	 * @access	public
	 * @return	SplObjectStorage
	 */
    public function getHandlers()
	{
        return $this->_observers;
    }

}

/**
 * You can attach an EventListener to an event to be notified when a specific
 * event has occured. Although unused, you can use 
 *
 * @author 	Thomas RAMBAUD
 * @version 1.0
 * @access 	public
 */
abstract class EventListener implements SplObserver {    

	// holds all states
	private $_states = array();
	
	/**
	 * Returns all states.
	 *
	 * @access	public
	 * @return	void
	 */
	public function getStates()
	{
		return $this->_states;
	}
	
	/**
	 * Adds a new state.
	 *
	 * @access	public
	 * @param	mixed	$state
	 * @param	int		$stateValue
	 * @return	void
	 */
	public function addState($state, $stateValue = 1)
	{
		$this->_states[$state] = $stateValue;
	}

	/**
	 * @Removes a state.
	 *
	 * @access	public
	 * @param	mixed	$state
	 * @return 	bool
	 */
	public function removeState($state)
	{
		if ($this->hasState($state)){
			unset($this->_states[$state]);
			return TRUE;   
		}        
		return FALSE;
	}

	/**
	 * Checks if a given state exists.
	 *
	 * @access	public
	 * @param	mixed	$state
	 * @return	bool
	 */
    public function hasState($state)
	{
        return isset($this->_states[$state]);        
    }
    
	/**
	 * Implementation of SplObserver::update().
	 *
	 * @access	public
	 * @param	SplSubject	$subject
	 * @param	mixed		$triggersMethod
	 * @param	mixed		&$arg			Any passed in arguments
	 */
    public function update(SplSubject $subject, $triggersMethod = NULL, &$arg = NULL) {
		if ($triggersMethod) {
			if (method_exists($this, $triggersMethod)) {
				$this->{$triggersMethod}($arg);
			} else {
				throw new Exception('The specified event method ' . get_called_class() . '::' . $triggersMethod . ' does not exist.');
			}
		} else {
			throw new Exception('The specified event method ' . get_called_class() . '::' . 'update() does not exist.');
		}
	}
	
}

/**
 * An example of creating an email notification event that gets triggered
 * when a new comment is added.
 */
class EmailNotification extends EventListener {

	public function notify(&$comment)
	{
		$recipients = array('dude@domain.com', 'lady@organization.org');
		foreach ($recipients as $email) {
			echo 'Notifying recipient ' . $email . PHP_EOL;
			echo 'Comment: ' . print_r($comment, true) . PHP_EOL;
			//mail($email, 'Comment added', $comment['body']);
		}
	}

}

/**
 * An example of creating a new comment logger that gets triggered when a
 * new comment is added.
 */
class CommentLogger extends EventListener {

	public function comment(&$comment)
	{
		echo 'Logging the comment:' . PHP_EOL;
		echo print_r($comment, true) . PHP_EOL;
		//error_log('notice', $comment) 
	}

}

//===================================================
// EXAMPLE USAGE OF THE ABOVE CLASSES BELOW THIS LINE
//===================================================

/**
 * Quick example function of adding a comment.
 */
function add_comment($comment_info, EventDispatcher $EventDispatcher)
{
	// insert the comment into the database
	$sql = sprintf('INSERT INTO comments SET created_by = %d, comment = %s, created_ts = %s',
			$comment_info['created_by'],
			'"' . mysql_real_escape_string($comment_info['comment']) . '"',
			'"' . time() . '"');

	// myqsl_query($sql);

	// notify any event listeners of onCommentAdded
	$EventDispatcher->onCommentAdded->notify($comment_info);
}

// load up an instance of the event handler
$EventDispatcher = new EventDispatcher();

// watch for comment being added and attach notification and logging
$EventDispatcher->onCommentAdded->bind(new EmailNotification(), 'notify');
$EventDispatcher->onCommentAdded->bind(new CommentLogger(), 'comment');

// trigger the bound events for add_comment
add_comment(
	array(
		'created_by' => 1,
    		'comment' => 'Lorem ipsum dolor sir amet.'
	),
	$EventDispatcher
);

/*
You can perform the same thing above by doing the following:

// add a new event
$Events->add('onCommentAdded');

// bind some event handlers to the event
$Events->get('onCommentAdded')->attach(new EmailNotification());
$Events->get('onCommentAdded')->attach(new CommentLogger());

This avoids using the magic method __get(), which is particularly slow.
It really depends on if you want to decrease readability.
*/