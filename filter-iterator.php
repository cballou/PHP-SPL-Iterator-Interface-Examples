<?php
/**
 * This class demonstrates a "real world" example of filtering users out from a
 * mock array. Pretend the data came from your model and you only need to match a subset
 * of the returned results. This class is an example of filtering any array
 * by either key or value with a few minor adjustments.
 * 
 * @Class FilterExample
 */
class ObjectFilter extends FilterIterator
{
    protected $_filterKey;
    protected $_filterVal;
    
    /**
     * Calls the parent FilterIterator constructor.
     *
     * @param   Iterator    $it     An iterator object
     * @param   mixed       $filterKey
     * @param   mixed       $filterVal
     *
     */
    public function __construct(Iterator $it, $filterKey, $filterVal)
    {
        parent::__construct($it);
        
        $this->_filterKey = $filterKey;
        $this->_filterVal = $filterVal;
    }

    /**
     * The accept method is required, as FilterExample
     * extends FilterIterator with abstract method accept().
     *
     * @access  public
     * @accept  Only allow values that are not ___
     * @return  string
     */
    public function accept()
    {
        $object = $this->getInnerIterator()->current();
        // base case
        if (!isset($object[$this->_filterKey])) return true;
        // if the key and value match the filter
        if (strcasecmp($object[$this->_filterKey], $this->_filterVal) == 0) {
            return false;
        }
        return true;
    }
}

// load up an append iterator
$it = new AppendIterator();

// create some example users as ArrayIterators
$user1 = new ArrayIterator(array('id' => 1, 'name' => 'George'));
$user2 = new ArrayIterator(array('id' => 2, 'name' => 'John'));
$user3 = new ArrayIterator(array('id' => 3, 'name' => 'Eric'));
$user4 = new ArrayIterator(array('id' => 4, 'name' => 'Jason'));
$user5 = new ArrayIterator(array('id' => 5, 'name' => 'Emanuel'));

// filter and append the ArrayIterators
$it->append(new ObjectFilter($user1, 'name', 'Eric'));
$it->append(new ObjectFilter($user2, 'name', 'Eric'));
$it->append(new ObjectFilter($user3, 'name', 'Eric'));
$it->append(new ObjectFilter($user4, 'name', 'Eric'));
$it->append(new ObjectFilter($user5, 'name', 'Eric'));

// show the example filtered output
foreach($it as $key => $val) {
    echo $key . ' = ' . $val . PHP_EOL;
}


/**
 * This is the same example, but utilising ArrayObject
 * instead of AppendIterator and ArrayIterator.
 */
$users = array(
    array('id' => 1, 'name' => 'George'),
    array('id' => 2, 'name' => 'John'),
    array('id' => 3, 'name' => 'Eric'),
    array('id' => 4, 'name' => 'Jason'),
    array('id' => 5, 'name' => 'Emanuel')
);

// convert users to ArrayObject
$users = new ArrayObject($users);

// filter out all user's with the name "John"
$it = new ObjectFilter($users->getIterator(), 'name', 'john');