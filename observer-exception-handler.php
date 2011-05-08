<?php
/**
 * This is an example of using the observer pattern to handle exceptions. You
 * can attach an arbitrary number of observers to the ExceptionHandler
 * for handling exceptions in different ways. This could be extensible by
 * adding more observers, or only having observers act on distinct types of
 * exceptions.
 *
 * The ExceptionHandler class sends uncaught exception messages to the proper
 * handlers. This is done using SplObserver/SplSubject.
*/
class ExceptionHandler implements SplSubject
{
    /**
     * An array of SplObserver objects to notify of Exceptions.
     *
     * @var array
     */
    private $_observers = array();

    /**
     * The uncaught Exception that needs to be handled.
     *
     * @var Exception
     */
    protected $_exception;

    /**
     * Constructor method for ExceptionHandler.
     *
     * @return ExceptionHandler
     */
    function __construct() { }

    /**
     * A custom method for returning the exception.
     *
     * @access  public
     * @return  Exception
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Attaches an SplObserver to the ExceptionHandler to be notified when an
     * uncaught Exception is thrown.
     *
     * @access  public
     * @param   SplObserver        The observer to attach
     * @return  void
     */
    public function attach(SplObserver $obs)
    {
        $id = spl_object_hash($obs);
        $this->_observers[$id] = $obs;
    }

    /**
     * Detaches the SplObserver from the ExceptionHandler, so it will no longer
     * be notified when an uncaught Exception is thrown.
     * 
     * @access  public
     * @param   SplObserver        The observer to detach
     * @return  void
     */
    public function detach(SplObserver $obs)
    {
        $id = spl_object_hash($obs);
        unset($this->_observers[$id]);
    }

    /**
     * Notify all observers of the uncaught Exception so they can handle it as
     * needed.
     *
     * @access  public
     * @return  void
     */
    public function notify()
    {
        foreach($this->_observers as $obs) {
            $obs->update($this);
        }
    }

    /**
     * This is the method that should be set as the default Exception handler by
     * the calling code.
     *
     * @access  public
     * @return  void
     */
    public function handle(Exception $e)
    {
        $this->_exception = $e;
        $this->notify();
    }
    
}

/**
 * The Logger exception handler is responsible for logging uncaught
 * exceptions to a file for debugging. It is an extension of what
 * would be your actual Logger class.
 */
class ExceptionLogger extends Logger implements SplObserver
{
    /**
     * Update the error_log with information about the Exception.
     *
     * @param   SplSubject  $subject   The ExceptionHandler
     * @return  bool
     */
    public function update(SplSubject $subject)
    {
        $exception = $subject->getException();
        
        $output = 'File: ' . $exception->getFile() . PHP_EOL;
        $output .= 'Line: ' . $exception->getLine() . PHP_EOL;
        $output .= 'Message: ' . PHP_EOL . $exception->getMessage() . PHP_EOL;
        $output .= 'Stack Trace:' . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
        
        echo "\n\nThe following message was sent to your default PHP error log:\n\n";
        echo $output;
        
        return error_log($output);
    }
}

/**
 * The Mailer exception handler is responsible for mailing uncaught
 * exceptions to an administrator for notifications. It is an extension
 * of what would be your actual Mailer class.
 */
class ExceptionMailer extends Mailer implements SplObserver
{
    
    /**
    * Mail the sysadmin with Exception information.
    *
    * @param    SplSubject $subject   The ExceptionHandler
    * @return   bool
    */
    public function update(SplSubject $subject)
    {
        $exception = $subject->getException();
        
        // perhaps emailer also would like to know the server in question
        $output = 'Server: ' . $_SERVER['HOSTNAME'] . PHP_EOL;
        $output .= 'File: ' . $exception->getFile() . PHP_EOL;
        $output .= 'Line: ' . $exception->getLine() . PHP_EOL;
        $output .= 'Message: ' . PHP_EOL . $exception->getMessage() . PHP_EOL;
        $output .= 'Stack Trace:' . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
    
        $headers = 'From: webmaster@yourdomain.com' . "\r\n" .
                    'Reply-To: webmaster@yourdomain.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

        echo "\n\nThe following email (would be) sent to your webmaster@yourdomain.com:\n\n";
        echo $output;

        //return mail('webmaster@yourdomain.com', 'Exception Thrown', $output, $headers);
    }
    
}

/**
 * Assume this Mailer class is your actual mailer class (i.e. SwiftMailer).
 */
class Mailer { }

/**
 * Assume this Logger class is your actual logger class.
 */
class Logger { }

//====================================
// BELOW THIS LINE RUNS THE ABOVE CODE
//====================================

// Create the ExceptionHandler  
$handler = new ExceptionHandler();  
  
// Attach an Exception Logger and Mailer  
$handler->attach(new ExceptionLogger());  
$handler->attach(new ExceptionMailer());  
  
// Set ExceptionHandler::handle() as the default  
set_exception_handler(array($handler, 'handle'));

// throw an exception for handling
throw new Exception("This is a test of the emergency broadcast system\n", 0);
