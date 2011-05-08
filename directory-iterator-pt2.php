<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'filter-dots.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'filter-extension.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'filter-key.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'directory-tree.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'directory-graph.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator' . DIRECTORY_SEPARATOR . 'directory-match.php');

/**
 * This class contains example usage for the entire directory-iterator sub
 * directory.
 * 
 * http://www.phpclasses.org/package/4389-PHP-Retrieve-directory-listings-with-SPL-iterators.html
 * 
 * @author Paul Scott <pscott@uwc.ac.za>
 */
class DirectoryUsage
{
    /**
     * Recursively list the contents of a directory. Second parameter allows you
     * to specify whether you'd like to only return a list of directories or files.
     *
     * @param   string  $dir
     * @param   string  $type ['file'|'dir']
     * @return  array
     */
    public function dirListByType($dir, $type = 'file')
    {
        $output = array();
        
        $it = new RecursiveDirectoryIterator($dir);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ($file->getType() == $type) {
                $output[] = $file->getPathname();
            }
        }
        
        return $output;
    }

    /**
     * Example of using SPL to clean up directories by removing specific filenames.
     *
     * @access  public
     * @param   string  $directory
     * @param   array   $filter
     */
    public function cleanDir($directory, $filter = array('_vti_cnf', '_vti_private', '_vti_txt', '_private', '_themes', 'msupdate', 'vti_pvt', 'vti_script', '_vti_log', '_template','Thumbs.db'))
    {
        $it = new RecursiveDirectoryIterator($directory);
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            
            // remove empty dirs
            if (sizeof($file->getSize()) == 0) {
                unlink($file->getPath());
            }
            
            // remove instances of Thumbs.db
            if ($file->getFileName() == 'Thumbs.db') {
                unlink($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename());
            }
            
            // if paths match filter, delete directory recursively
            $parts = explode(DIRECTORY_SEPARATOR, $file->getPath());
            if(in_array(end($parts), $filter)) {
                $this->deleteDir($file->getPath());
            }
            
        }
    }

    /**
     * Method to get information about all the files in a directory (recursive)
     *
     * @param   string  $directory
     * @param   array   $filter
     * @return  array
     */
    public function fileInfo($directory, $filter = array('php', 'xsl', 'xml', 'htm', 'html','css'))
    {
        $count_directories = 0;
        $count_files = 0;
        $count_lines = 0;
        $count_bytes = 0;

        $it = new RecursiveDirectoryIterator($directory);
        $it = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($it as $file) {
            if (false === $file->isDir()) {
                // get the file extension
                $ext = $file->getExtension();
                if (in_array($ext, $filter)) {
                    $count_files++;
                    $count_bytes += $file->getSize();
                    $count_lines += sizeof(explode("n", file_get_contents($file->getPathName())));
                }
            } else if(false === strpos($file->getPathname(), 'CVS') && $file->isDir()) {
                $count_directories++;
            }
        }

        return array(
            'bytes'       => $count_bytes,
            'files'       => $count_files,
            'lines'       => $count_lines,
            'directories' => $count_directories
        );
    }

    /**
     * Recursively delete a directory and all subdirectories.
     *
     * @access  public
     * @param   string  $dir
     * @return  void
     */
    public function deleteDir($dir)
    {
        $it = new RecursiveDirectoryIterator($dir);
        $it = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
                @rmdir($dir);
            }
        }
        @rmdir($dir);
    }

    /**
     * Find a file by regex in a given directory.
     *
     * @param string $path
     * @param string $regex
     * @return array
     */
    public function fileFinder($path, $regex)
    {
        $matches = array();
        
        $fileList = new DirMatch($path, $regex);
        foreach ($fileList as $file) {
            $matches[] = $file;
        }
        
        return $matches;
    }
    
    /**
     * List files in a given directory.
     *
     * @param string $dir
     * @return array
     */
    public function fileLister($dir)
    {
        $files = array();
        
        $filtered = new DirectoryFilterDots($dir);
        foreach ($filtered as $file) {
            if ($file->isDir()) {
                continue;
            } 
            $files[] = $file->getFilename();
        }
        
        return $files;
    }
    
}

// generate the directory path to the example dir
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'directory-iterator-example';

// load up the class
$DirectoryUsage = new DirectoryUsage();

echo '==================================' . PHP_EOL;
echo 'Recursively show all files in a directory.' . PHP_EOL;
echo '==================================' . PHP_EOL;

$files = $DirectoryUsage->dirListByType($dir, 'file');
foreach ($files as $f) {
    echo $f . PHP_EOL;
}

echo '==================================' . PHP_EOL;
echo 'Recursively show all directories in a directory.' . PHP_EOL;
echo '==================================' . PHP_EOL;

$dirs = $DirectoryUsage->dirListByType($dir, 'dir');
foreach ($dirs as $d) {
    echo $d . PHP_EOL;
}

echo '==================================' . PHP_EOL;
echo 'Recursively iterate over all files in a directory.' . PHP_EOL;
echo '==================================' . PHP_EOL;

// recursively generate a tree representation
$files = new DirectoryTreeIterator($dir);
foreach ($files as $f) {
    echo $f . PHP_EOL;
}

echo '==================================' . PHP_EOL;
echo 'Iterate over a all files in a directory, filtering out dots.' . PHP_EOL;
echo '==================================' . PHP_EOL;

// recursively generate a tree representation
$files = new DirectoryFilterDots($dir);
foreach ($files as $f) {
    echo $f . PHP_EOL;
}

echo '==================================' . PHP_EOL;
echo 'Find all files with a PHP extension.' . PHP_EOL;
echo '==================================' . PHP_EOL;

// filter by PHP file extension
$phpFiles = new ExtensionFilter(new DirectoryIterator($dir), 'php', $whitelist=true);
foreach ($phpFiles as $f) {
    echo $f->getPathName() . PHP_EOL;
}

echo '==================================' . PHP_EOL;
echo 'Find all files without a PHP extension.' . PHP_EOL;
echo '==================================' . PHP_EOL;

// filter by PHP file extension
$phpFiles = new ExtensionFilter(new DirectoryIterator($dir), 'php', $whitelist=false);
foreach ($phpFiles as $f) {
    echo $f->getPathName() . PHP_EOL;
}