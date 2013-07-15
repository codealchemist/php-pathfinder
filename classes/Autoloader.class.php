<?php
/**
 * Handles class autoloading.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Autoloader {
    private $cacheFile = 'autoloader.json';
    private $classPathMap = array();
    
    /**
     * Constructs Autoloader looking for cached class paths.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function __construct(){
        spl_autoload_register(array($this, 'loader'));
        
        //try to load cache
        $cacheFile = dirname(__FILE__) . "/{$this->cacheFile}";
        if(file_exists($cacheFile)) $this->loadCache($cacheFile);
    }
    
    /**
     * Loads cached class paths.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $cacheFile
     */
    private function loadCache($cacheFile){
        $raw = readfile($cacheFile);
        $this->classPathMap = json_decode($raw);
    }
    
    /**
     * Dirs where to look for Classes.
     * @var array 
     */
    private $dirs = array('classes');
    
    /**
     * Class file map.
     * @var array 
     */
    private $classes = array();
    
    /**
     * Dynamically loads used classes.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $className 
     */
    private function loader($className) {
        //try to get class path from already mapped ones
        if(!empty($this->classPathMap)){
            if(array_key_exists($className, $this->classPathMap)){
                require_once $this->classPathMap[$className];
                return true;
            }
        }
        
        //try to load class from configured class dirs
        foreach($this->dirs as $dir){
            $classFile = dirname(__FILE__) . "/../$dir/$className.class.php";
            if(!file_exists($classFile)) continue;
            
            //store class path map and require class
            $this->addCache($className, $classFile);
            require_once $classFile;
            return true;
        }
        
        die(__CLASS__ . ": FAILED TO LOAD REQUIRED CLASS: $className\n\n");
    }
}