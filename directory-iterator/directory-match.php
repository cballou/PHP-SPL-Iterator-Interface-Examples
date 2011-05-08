<?php
/**
 * Handle matching and filtering directories by a regular expression.
 */
class DirectoryMatch extends KeyFilter
{
    public function __construct($path , $regex)
    {
        parent::__construct(new DirTreeIterator($path), $regex);
    }

    /**
     * Override the current element to simply return the key.
     *
     * @access  public
     * @return  string
     */
    public function current()
    {
        return parent::key();
    }

    /**
     * Override the key element to simply return the key.
     *
     * @access  public
     * @return  string
     */
    public function key()
    {
        return parent::key();
    }
}