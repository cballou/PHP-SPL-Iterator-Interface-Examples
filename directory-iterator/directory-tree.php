<?php
class DirectoryTreeIterator extends RecursiveIteratorIterator
{
    /** 
     * Construct from a path.
     * @param $path directory to iterate
     */
    public function __construct($path)
    {
        try {
            parent::__construct(
                new RecursiveCachingIterator(
                    new RecursiveDirectoryIterator(
                        $path,
                        RecursiveDirectoryIterator::KEY_AS_FILENAME
                    ),
                    CachingIterator::CALL_TOSTRING|CachingIterator::CATCH_GET_CHILD
                ),
                parent::SELF_FIRST
            );
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Skip over elements with children, returning keys.
     *
     * @access  public
     * @return  string
     */
    public function current()
    {
        if ($this->hasChildren()) {
            $this->next();
        }
        return $this->getInnerIterator()->current()->getPath() . DIRECTORY_SEPARATOR . $this->key();
    }

    /**
     * An aggregate of the inner iterator.
     *
     * @access  public
     * @param   string  $func
     * @param   mixed   $params
     */
    public function __call($func, $params)
    {
        return call_user_func_array(array($this->getSubIterator(), $func), $params);
    }
}