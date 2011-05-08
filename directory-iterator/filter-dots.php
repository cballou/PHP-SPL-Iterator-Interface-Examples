<?php
/**
 * Directory iterator class - filters out the . and .. directories
 *
 */
class DirectoryFilterDots extends RecursiveFilterIterator
{
    /**
     * Init with a recursive directory iterator.
     *
     * @access  public
     * @param   RecursiveDirectoryIterator  $path   The directory to iterate
     */
    public function __construct($path)
    {
        parent::__construct(new RecursiveDirectoryIterator($path));
    }

    /**
     * Filter out both kinds of dots in a directory structure.
     *
     * @access  public
     * @return  bool    Whether the current entry is neither '.' nor '..'
     */    
    public function accept()
    {
        return !$this->getInnerIterator()->isDot();
    }

    /**
     * Override the key method to return the path name.
     *
     * @access  public
     * @return  string  The current entries path name
     */
    public function key()
    {
        return $this->getInnerIterator()->getPathname();
    }
}