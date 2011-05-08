<?php
/**
 * Filters out files with specified extensions.
 */
class ExtensionFilter extends FilterIterator {

    private $_ext;
    private $_it;
    private $_whitelisted;

    /**
     * Takes both a directory iterator and a file extension and only returns
     * results matching the particular extension.
     *
     * @access  public
     */
    public function __construct(DirectoryIterator $it, $ext, $whitelisted = false)
    {
        parent::__construct($it);
        $this->_it = $it;
        $this->_ext = $ext;
        $this->_whitelisted = $whitelisted;
    }

    /**
     * Given the current iterator position, check the filename against
     * the extension and filter accordingly.
     *
     * @access  public
     * @return  bool
     */
    public function accept()
    {
        $return = true;

        // skip dots
        if ($this->_it->isDot()) return false;

        // pop off the extension for non-directories and try to match
        if (!$this->_it->isDir()) {
            $ext = $this->_it->getExtension();

            if ($this->_whitelisted) {
                if (is_array($this->_ext)) {
                    $return = in_array($ext, $this->_ext);
                } else {
                    $return = $ext === $this->_ext;
                }
            } else {
                if (is_array($this->_ext)) {
                    $return = !in_array($ext, $this->_ext);
                } else {
                    $return = $ext !== $this->_ext;
                }
            }
        }
        
        return $return;
    }
}