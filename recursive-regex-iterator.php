<?php
/**
 * This example recursively finds all PHP files from a given starting directory.
 * The regular expression must also match directory names (i.e. no file extension)
 * as it's part of the filtering process.
 */

// the starting directory
$directory = __DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator-example';

$dir = new RecursiveIteratorIterator(
	new RecursiveRegexIterator(
		new RecursiveDirectoryIterator(
			$directory, 
			RecursiveDirectoryIterator::FOLLOW_SYMLINKS
		), 
		// match both php file extensions and directories
		'#(?<!/)\.php$|^[^\.]*$#i'
	), 
	true
);

// output all matches
echo PHP_EOL . 'PHP files contained in directory ' . $directory . PHP_EOL . PHP_EOL;
if (!empty($dir)) {
	foreach ($dir as $d) {
		if (strpos($d->getFilename(), '.php') !== FALSE) {
			echo $d->getPath() . DIRECTORY_SEPARATOR . $d->getFilename() . PHP_EOL;
		}
	}
}