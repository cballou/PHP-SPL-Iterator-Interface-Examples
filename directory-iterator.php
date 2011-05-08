<?php
/**
 * The below example is the simplest case of iterating over all direct children of a directory,
 * without recursing into sub-directories. The "." and ".." references are skipped.
 */

// iterate over all files from the child directory "directory-iterator-example"
$files = new DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator-example');
foreach ($files as $file) {
    // skip over dots ("." and "..")
    if (!$file->isDot()) {
        // example of the 
        echo $file->getRealPath() . PHP_EOL;
    }
}