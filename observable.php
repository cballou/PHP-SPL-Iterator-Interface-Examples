<?php
/**
 * A class can implement the Observer interface when it wants to be notified of
 * changes in "observable" objects. The SPL library has contained the SplObserver
 * interface and SplSubject class since PHP 5.1.
 *
 * This class represents an observable object, or "data" in the model-view paradigm.
 * It can be subclassed to represent an object that the application wants to have
 * observed. An observable object can have one or more observers. An observer may
 * be any object that implements interface Observer. After an observable instance
 * changes, an application calling the Observable's notifyObservers method causes
 * all of its observers to be notified of the change by a call to their update method.
 * 
 * The order in which notifications will be delivered is unspecified. The default
 * implementation provided in the Observerable class will notify Observers in the
 * order in which they registered interest, but subclasses may change this order,
 * use no guaranteed order, deliver notifications on separate threads, or may
 * guarantee that their subclass follows this order, as they choose.
 */ 
class Observable implements SplSubject
{

    /**
     * @var array $observers list of observer objects
     */         
    protected $_observers = array();
    
    /**
     * Construct an Observable with zero Observers.
     *
     * @access  public
     * @return  void
     */         
    public function __construct() { }
    
    /**
     * Adds an observer to the set of observers for this object, provided that it is not the same as some observer already in the set.
     *
     * @access  public
     * @param   SplObserver $observer   an observer to be added.
     * @return  void
     */                             
    public function addObserver(SplObserver $observer)
    {
        // ensure the observer doesn't already exist
        if (!$this->containsObserver($observer)) {
            $this->_observers[] = $observer;    
        }
    }
    
    /**
     * Deletes an observer from the set of observers contained within
     * this particular observable.
     *
     * @access  public
     * @param   Observer $observer the observer to be deleted.
     * @return  void
     */
    public function deleteObserver(SplObserver $observer)
    {
        if ($this->containsObserver($observer)) {
            $this->observers = array_diff($this->_observers, array($observer));
        }
    }
    
    /**
     * Clears the observer list so that this object no longer has any observers.
     *
     * @access  public
     * @return  void
     */
    public function deleteObservers()
    {
        unset($this->_observers);
        $this->_observers = array();
    }
    
    /**
     * If this object has changed, as indicated by the hasChanged method, then
     * notify all of its observers and then call the clearChanged method to
     * indicate that this object has no longer changed.
     *
     * @access  public
     * @return  void
     */
    public function notifyObservers()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);        
        }
    }
    
    /**
     * Returns the number of observers of this Observable object.
     *
     * @access  public
     * @return  int     the number of observers of this object.
     */                   
    public function countObservers()
    {
        return count($this->_observers);
    }

    /**
     * Check if observer already exists in the list.
     *
     * @param   SplObserver $observer
     * @return  bool
     */
    public function containsObserver(SplObserver $observer)
    {
        return in_array($observer, $this->_observers);
    }
    
    /**
     * Add an observer.
     *
     * @access  public
     * @param   SplObserver $observer
     * @return  void
     */
    
    public function attach(SplObserver $observer)
    {
        $this->addObserver($observer);
    }
    
    /**
     * Remove an observer.
     *
     * @access  public
     * @param   SplObserver $observer
     * @return  void
     */
    public function detach(SplObserver $observer)
    {
        $this->deleteObserver($observer);
    }
    
    /**
     * Notify observers of a change.
     *
     * @access  public
     * @return  void
     */
    public function notify()
    {
        $this->notifyObservers();
    }

} 


//============================
// example usage of Observable
//============================

class KillBot implements SplObserver
{
    public function update(SplSubject $subject)
    {
        echo __CLASS__ . " says kill all humans." . PHP_EOL;
    }
}

class LoveBot implements SplObserver
{
    public function update(SplSubject $subject)
    {
        echo __CLASS__ . " says kiss all humans." . PHP_EOL;
    }
}

// load the observable (SPLSubject)
$robots = new Observable();

// load some observers
$killbot = new KillBot();
$lovebot = new LoveBot();

// add the observers to the observable
$robots->addObserver($killbot);
$robots->addObserver($lovebot);

// notify the observers of an event
$robots->notify(); 

/*
Observers output:

KillBot says kill all humans
LoveBot says kiss all humans
*/
