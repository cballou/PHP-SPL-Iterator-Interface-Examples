<?php
/**
 * This is a very basic example of using the regex iterator to perform a
 * replacement. In this case, we are merely performing a swap of the
 * pattern matches (test) and (0-9+).
 *
 * You can specify any of the following modes:
 *
 * RegexIterator::MATCH         Only execute match (filter) for the current entry.
 * RegexIterator::GET_MATCH     Return the first match for the current entry.
 * RegexIterator::ALL_MATCHES   Return all matches for the current entry.
 * RegexIterator::SPLIT         Returns the split values for the current entry.
 * RegexIterator::REPLACE       Replace the current entry.
 * RegexIterator::USE_KEY       Special flag: Match the entry key instead of the entry value.
 */
$a = new ArrayIterator(array('test1', 'test2', 'test3'));
$i = new RegexIterator($a, '/^(test)(\d+)/', RegexIterator::REPLACE);
$i->replacement = '$2:$1';

print_r(iterator_to_array($i));
/*
Array
(
    [0] => 1:test
    [1] => 2:test
    [2] => 3:test
)
*/ 

