<?php
/**
 * An example of using the caching iterator to perform a look-ahead for the last element
 * in a single dimension "navigation" array so we can accurately set classes for "last".
 *
 * Please note:
 * No safety measures have been taken to sanitize the output.
 *
 * @author  Corey Ballou
 */

// example navigation array
$nav = array(
    'Home' => '/home',
    'Products' => '/products',
    'Company' => '/company',
    'Privacy Policy' => '/privacy-policy'
);

// storage of output
$output = new ArrayIterator();

try {
    
    // create the caching iterator of the nav array
    $it = new CachingIterator(new ArrayIterator($nav));
    foreach ($it as $name => $url) {
        if ($it->hasNext()) {
            $output->append('<li><a href="' . $url . '">' . $name . '</a></li>');
        } else {
            $output->append('<li class="last"><a href="' . $url . '">' . $name . '</a></li>');
        }
    }
    
    // if we have values, output the unordered list
    if ($output->count()) {
        echo '<ul id="nav">' . "\n" . implode("\n", (array) $output) . "\n" . '</ul>';
    }
    
} catch (Exception $e) {
    die($e->getMessage());
}

/**
 * Below is the same example, but prettified in a nice, extensible class
 * allowing you to reuse it for nav, subnav, or any time you need to
 * determine the last element of an array.
 */
class NavBuilder extends CachingIterator {
    
    /**
     * Override the current() method to modify the return value
     * for the given index.
     *
     * @access  public
     * @return  string
     */
    public function current()
    {
        // get the name and url of the nav item
        $name = parent::key();
        $url = parent::current();
        
        // determine if we're on the last element
        if ($this->hasNext()) {
            return '<li><a href="' . $url . '">' . $name . '</a></li>';
        } else {
            return '<li class="last"><a href="' . $url . '">' . $name . '</a></li>';
        }
    }
    
    /**
     * Outputs the navigation.
     */
    public function generate()
    {
        $inner = $this->getInnerIterator();
        var_dump(get_class_methods($inner));
    }
    
}

try {
    $it = new NavBuilder(new ArrayIterator($nav));
    echo $it->generate();
} catch (Exception $e) {
    var_dump($e); die;
}