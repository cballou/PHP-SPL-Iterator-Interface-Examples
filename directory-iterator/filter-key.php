<?php
/**
 * Filter an array of string results based on a given regular expression.
 */
class KeyFilter extends FilterIterator
{
    private $_regex;

    /**
     * The key filter takes in an iterator and a regular expression pattern
     * to filter the iterator keys against.
     *
     * @access  public
     * @return  void
     */
    public function __construct(Iterator $it, $regex)
    {
        parent::__construct($it);
        $this->_regex = $regex;
    }

    /**
     * Provide the required accept() method for filtering keys by
     * a regular expression.
     *
     * @access  public
     * @return  int|bool
     */
    public function accept()
    {
        return preg_match($this->_regex, $this->getInnerIterator()->key());
    }

    /**
     * Override the cloning method.
     *
     * @access  protected
     * @return  bool
     */
    protected function __clone() {
        return false;
    }
}