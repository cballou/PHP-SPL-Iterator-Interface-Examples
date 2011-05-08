<?php
/**
 * The main use case of a limit iterator is pagination. Below is an example
 * Pagination class which takes an array (or object) iterator as an argument
 * and allows pagination on the set.
 *
 * @author	Corey Ballou
 */

class Paginator extends LimitIterator {
	
	// stores the array iterator
	protected $_it;
	
	// stores the current page
	protected $_currentPage;
	
	// stores the max number of items to display per page
	protected $_limit;
	
	// stores the number of array items
	protected $_count;
	
	// stores the total pages in the resultset
	protected $_totalPages;
	
	/**
	 * Default constructor to load the iterator. Override the parent
	 * LimitIterator as we don't want to return 
	 *
	 * @access	public
	 * @param	ArrayIterator	$it
	 */
	public function __construct(ArrayIterator $it, $page = 1, $limit = 10)
	{
		$this->_it = $it;
		$this->_count = $it->count();
		
		$this->setCurrentPage($page);
		$this->setItemsPerPage($limit);
	}
	
	/**
	 * Set the number of items to display per page.
	 *
	 * @access	public
	 * @param	int		$count
	 */
	public function setItemsPerPage($count = 10)
	{
		$this->_itemsPerPage = (int) $count;
		$this->_totalPages = ($this->_count > $this->_itemsPerPage) ? ceil($this->_count / $this->_itemsPerPage) : 1;
	}
	
	/**
	 * Set the current page (offset).
	 *
	 * @access	public
	 * @param	int		$page
	 */
	public function setCurrentPage($page = 1)
	{
		$this->_currentPage = (int) $page;
	}
	
	/**
	 * Returns the current page.
	 *
	 * @access	public
	 * @return	int
	 */
	public function getCurrentPage()
	{
		return $this->_currentPage;
	}
	
	/**
	 * Determines if another page exists.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function hasNextPage()
	{
		return $this->_currentPage < $this->_totalPages;
	}
	
	/**
	 * Determines if a previous page exists.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function hasPreviousPage()
	{
		return $this->_currentPage > 1;
	}
	
	/**
	 * Returns (fake render) the items matching the specific requirements.
	 *
	 * @access	public
	 * @param	mixed	$page
	 * @param	mixed	$limit
	 * @return	mixed
	 */
	public function render($page = NULL, $limit = NULL)
	{
		if (!empty($page)) {
			$this->setCurrentPage($page);
		}
		
		if (!empty($limit)) {
			$this->setItemsPerPage($limit);
		}
		
		// quickly calculate the offset based on the page
		if ($page > 0) $page -= 1;
		$offset = $page * $this->_itemsPerPage;
		
		// return the limit iterator
		return new LimitIterator($this->_it, $offset, $this->_itemsPerPage);
	}
	
}

// generate an example of page items to iterate over
$items = array(
	array('id' => 1, 'name' => 'Item 1', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 2, 'name' => 'Item 2', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 3, 'name' => 'Item 3', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 4, 'name' => 'Item 4', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 5, 'name' => 'Item 5', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 6, 'name' => 'Item 6', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 7, 'name' => 'Item 7', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 8, 'name' => 'Item 8', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 9, 'name' => 'Item 9', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 10, 'name' => 'Item 10', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 11, 'name' => 'Item 11', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 12, 'name' => 'Item 12', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 13, 'name' => 'Item 13', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 14, 'name' => 'Item 14', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 15, 'name' => 'Item 15', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 16, 'name' => 'Item 16', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 17, 'name' => 'Item 17', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 18, 'name' => 'Item 18', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 19, 'name' => 'Item 19', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 20, 'name' => 'Item 20', 'desc' => 'Description', 'price' => 4.99),
	array('id' => 21, 'name' => 'Item 21', 'desc' => 'Description', 'price' => 4.99)
);

// load the paginator
$Paginator = new Paginator(new ArrayIterator($items));

// displays the initial set (page 1, limit 10)
$results = $Paginator->render();
foreach ($results as $r) {
	var_dump($r);
}

// check for another page
if ($Paginator->hasNextPage()) {
	echo 'DISPLAYING THE NEXT SET OF RESULTS AS AN EXAMPLE' . PHP_EOL;
	// displays the next page results as an example
	$results = $Paginator->render($Paginator->getCurrentPage() + 1);
	foreach ($results as $r) {
		var_dump($r);
	}
}