<?php
/**
 * This is an example of using the recursive caching iterator to do a lookahead
 * for the last element in a multi-dimensional "navigation" array so we can
 * accurately set classes for "last". The other functionality of generating
 * a full unordered list is just a bonus.
 *
 * Please note:
 * No safety measures have been taken to sanitize the output.
 *
 * @author  Corey Ballou
 */

// example navigation array
$nav = array(
    'Home' => '/home',
    'Fake' => array(
        'Double Fake' => array(
            'Nested Double Fake' => '/fake/double/nested',
            'Doubly Nested Double Fake' => '/fake/double/doubly'
        ),
        'Triple Fake' => '/fake/tripe'
    ),
    'Products' => array(
        'Product 1' => '/products/1',
        'Product 2' => '/products/2',
        'Product 3' => '/products/3',
        'Nested Product' => array(
            'Nested 1' => '/products/nested/1',
            'Nested 2' => '/products/nested/2'
        )
    ),
    'Company' => '/company',
    'Privacy Policy' => '/privacy-policy'
);

// storage of output
$output = new ArrayIterator();

try {
    
    // create the caching iterator of the nav array
    $it = new RecursiveIteratorIterator(
        new RecursiveCachingIterator(
            new RecursiveArrayIterator($nav)
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    // child flag
    $depth = 0;
    
    // generate the nav
    foreach ($it as $name => $url) {
        
        // set the current depth
        $curDepth = $it->getDepth();
        
        // store the difference in depths
        $diff = abs($curDepth - $depth);

        // close previous nested levels
        if ($curDepth < $depth) {
            $output->append(str_repeat('</ul></li>', $diff));
        }
        
        // check if we have the last nav item
        if ($it->hasNext()) {
            $output->append('<li><a href="' . $url . '">' . $name . '</a>');
        } else {
            $output->append('<li class="last"><a href="' . $url . '">' . $name . '</a>');
        }
        
        // either add a subnav or close the list item
        if ($it->hasChildren()) {
            $output->append('<ul>');
        } else {
            $output->append('</li>');
        }
        
        // cache the depth
        $depth = $curDepth;
    }
    
    // if we have values, output the unordered list
    if ($output->count()) {
        echo '<ul id="nav">' . "\n" . implode("\n", (array) $output) . "\n" . '</ul>';
    }
    
} catch (Exception $e) {
    die($e->getMessage());
}


echo PHP_EOL . PHP_EOL . 'CLASS EXAMPLE' . PHP_EOL . PHP_EOL;


/**
 * Below is the same example, but prettified in a nice, extensible class
 * allowing you to reuse it for nav, subnav, or any time you need to
 * determine the last element of an array.
 */
class NavBuilder extends RecursiveIteratorIterator {
    
    // stores the previous depth
    private $_depth = 0;
    
    // stores the current iteration's depth
    private $_curDepth = 0;
    
    // store the iterator
    protected $_it;
    
    /**
     * Constructor.
     *
     * @access  public
     * @param   Traversable $it
     * @param   int         $mode
     * @param   int         $flags
     */
    public function __construct(Traversable $it, $mode = RecursiveIteratorIterator::SELF_FIRST, $flags = 0)
    {
        parent::__construct($it, $mode, $flags);
        
        // store the caching iterator
        $this->_it = $it;
    }
    
    /**
     * Override the return values.
     *
     * @access  public
     */
    public function current()
    {
        // the return output string
        $output = '';
        
        // set the current depth
        $this->_curDepth = parent::getDepth();
        
        // store the difference in depths
        $diff = abs($this->_curDepth - $this->_depth);
        
        // get the name and url of the nav item
        $name = parent::key();
        $url = parent::current();

        // close previous nested levels
        if ($this->_curDepth < $this->_depth) {
            $output .= str_repeat('</ul></li>', $diff);
        }
        
        // check if we have the last nav item
        if ($this->hasNext()) {
            $output .= '<li><a href="' . $url . '">' . $name . '</a>';
        } else {
            $output .= '<li class="last"><a href="' . $url . '">' . $name . '</a>';
        }
        
        // either add a subnav or close the list item
        if ($this->hasChildren()) {
            $output .= '<ul>';
        } else {
            $output .= '</li>';
        }
        
        // cache the depth
        $this->_depth = $this->_curDepth;

        // return the output ( we could've also overridden current())
        return $output;
    }
    
}

//======
// usage
//======

try {
    
    // generate the recursive caching iterator
    $it = new RecursiveCachingIterator(new RecursiveArrayIterator($nav));
    
    // build the navigation with the iterator
    $it = new NavBuilder($it, RecursiveIteratorIterator::SELF_FIRST);

    // display the resulting navigation
    echo '<ul id="nav">' . PHP_EOL;
    foreach ($it as $value) {
        echo $value . "\n";
    }
    echo '</ul>' . PHP_EOL;

} catch (Exception $e) {
    var_dump($e); die;
}