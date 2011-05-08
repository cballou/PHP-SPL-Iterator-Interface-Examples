<?php
/**
 * This is a basic example of recursively iterating over a directory,
 * skipping both "." and "..".
 */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator-example')
);

// iterate over all files from the child directory "directory-iterator-example"
foreach ($files as $file) {
    // skip over dots ("." and "..")
    if (!$file->isDot()) {
        // example of the 
        echo $file->getRealPath() . PHP_EOL;
    }
}