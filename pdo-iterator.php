<?php
/**
 * This example demonstrates conversion of a PDO resultset into a tabular view.
 * It does not currently support 
 */
class TableRows extends RecursiveIteratorIterator {
    
    // store the iterator
    protected $_it;
    
    // the PDO
    protected $_dsn;
    
    // teh table name
    protected $_table;
    
    /**
     * Load the iterator.
     */
    public function __construct($it, $dsn, $table) {
        $this->_it = $it;
        $this->_dsn = $dsn;
        $this->_table = $table;

        parent::__construct($this->_it, self::LEAVES_ONLY);
    } 

    /**
     * Generates a table header based on the metadata.
     *
     * @access  public
     * @return  string
     */
    public function getHeader()
    {
        $output = '<table cellpadding="0" cellspacing="0" style="width:100%;border:1px solid #000; padding: 4px;">' . PHP_EOL;
        $output .= '<thead>' . PHP_EOL;
        $output .= '<tr>';
        
        $results = $this->_dsn->query(sprintf('SHOW COLUMNS FROM %s', $this->_table));
        foreach ($results as $r) {
            $output .= '<th>' . $r['Field'] . '</th>';
        }

        $output .= '</tr>' . PHP_EOL;
        $output .= '</thead>' . PHP_EOL;
        $output .= '<tbody>' . PHP_EOL;
        echo $output;
    }
    
    /**
     * Get the body.
     *
     * @access  public
     * @return  string
     */
    public function getBody()
    {
        $output = '';
        while ($this->valid()) {
            echo '<td>' . $this->current() . '</td>';
            $this->next();
        }
    }
    
    /**
     * Generates the table footer.
     *
     * @access  public
     * @return  string
     */
    public function getFooter()
    {
        echo '</tbody></table>';
    }

    /**
     * Create a new table row.
     *
     * @access  public
     * @return  string
     */
    public function beginChildren() {
        echo '<tr>';
    }

    /**
     * Close a table row.
     *
     * @access  public
     * @return  string
     */
    public function endChildren() { 
        echo '</tr>' . PHP_EOL;
    }

}

try {
    
    // load the database via PDO
    $dsn = new PDO('mysql:dbname=testdb;host=127.0.0.1');

    // the result only implements Traversable
    $stmt = $dsn->prepare('SELECT * FROM test');

    // exceute the query
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    // get the results
    $results = $stmt->fetchAll();

    // generate the recursive iterator
    $it = new RecursiveArrayIterator($results);
    $TableRows = new TableRows($it, $dsn, 'test');
    
    // output the table
    $TableRows->getHeader();
    $TableRows->getBody();
    $TableRows->getFooter();

} catch (PDOException $e) {
    die($e->getMessage());
}

/*
CREATE DATABASE testdb;
USE testdb;

CREATE TABLE test (
    `id` int(11) unsigned auto_increment primary key,
    `name` varchar(32),
    `description` varchar(255),
    `created` int(11) unsigned
);

INSERT INTO test (id, name, description, created) VALUES
(1, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(2, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(3, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(4, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(5, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(6, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(7, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(8, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(9, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW())),
(10, 'Entry 1', 'Description', UNIX_TIMESTAMP(NOW()));
*/
