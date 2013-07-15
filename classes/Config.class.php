<?php
/**
 * Reads config and provide tools to use it.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Config {
    public static $configFile = null;
    public static $mapsDir = 'maps';
    public static $algorithmsDir = 'algorithms';
    private static $omitFiles = array('.', '..', '.svn');
    private static $maps = array();
    private static $algorithms = array();
    public static $mapSufix = 'map.txt';
    public static $algorithmSufix = 'algorithm.class.php';
    private static $nodeRepresentations = array(
        NodeType::FREE => '.',
        NodeType::WALL => '|',
        NodeType::SOURCE => '0',
        NodeType::DESTINATION => '1',
        NodeType::CURRENT => '*'
    );
    private static $directionRepresentation = array(
        Direction::UP => '^',
        Direction::DOWN => 'V',
        Direction::LEFT => '<',
        Direction::RIGHT => '>',
        Direction::UPLEFT => '\\',
        Direction::UPRIGHT => '/',
        Direction::DOWNRIGHT => '\\',
        Direction::DOWNLEFT => '/'
    );
    public static $walkableNodeTypes = array(
        NodeType::FREE,
        NodeType::DESTINATION
    );
    public static $maxIterations = 400;
    
    /**
     * Inits Config loading initial config.
     * Dynamically load all config keys of config array as class properties.
     * Config dir should be named: "config"
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $configFile Config file to use
     */
    public static function init($configFile='default.config.php') {
        self::$configFile = $configFile;
        $configFile = "config/$configFile";
        if(!file_exists($configFile)) die("Oops! Config file not exists! File: '$configFile'");
        require_once $configFile;
        if(!isset($config)) die("Oops! $configFile should define \$config array!");
        
        self::load($config);
    }
    
    /**
     * Loads main config from file into Config object.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    private static function load($config){
        Debug::out("LOADING CONFIG...", __METHOD__);
        if(empty($config)) return false;
        foreach($config as $key => $value) self::${$key} = $value;
    }
    
    /**
     * Return array of available Maps.
     * Maps files should be named as: [mapName].map.txt
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public static function getMaps(){
        if(!empty(self::$maps)) return self::$maps;
        
        //iterate MAPS dir to get all available maps
        $dirContent = scandir(self::$mapsDir); //using scandir to get files and dirs ordered alphabethically
        foreach($dirContent as $file){
            //skip unwanted files and dirs
            if(in_array($file, self::$omitFiles) or is_dir($file)) continue;
            
            $sufix = self::$mapSufix;
            preg_match("/^(.*?)\.$sufix/", $file, $matches);
            if(empty($matches)) continue; //do nothing with non matching files
            $key = $matches[1];
            self::$maps[$key] = $file;
        }
        return self::$maps;
    }
    
    /**
     * Return array of available Algorithms.
     * Algorithm files should be named as: [algorithmName].algorithm.class.php 
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public static function getAlgorithms(){
        if(!empty(self::$algorithms)) return self::$algorithms;
        
        //iterate ALGORITHMS dir to get all available maps
        $dirContent = scandir(self::$algorithmsDir); //using scandir to get files and dirs ordered alphabethically
        foreach($dirContent as $file){
            //skip unwanted files and dirs
            if(in_array($file, self::$omitFiles) or is_dir($file)) continue;
            
            $sufix = self::$algorithmSufix;
            preg_match("/^(.*?)\.$sufix/", $file, $matches);
            if(empty($matches)) continue; //do nothing with non matching files
            $key = $matches[1];
            self::$algorithms[$key] = $file;
        }
        return self::$algorithms;
    }
    
    /**
     * Returns node representation for passed node type.
     * 
     * @param NodeType $type
     * @return string 
     */
    public static function getNodeRepresentation($type){
        if(!array_key_exists($type, self::$nodeRepresentations)) return false;
        return self::$nodeRepresentations[$type];
    }
    
    /**
     * Returns direction representation for passed direction.
     * 
     * @param Direction $direction
     * @return string
     */
    public static function getDirectionRepresentation($direction){
        if(!array_key_exists($direction, self::$directionRepresentation)) return false;
        return self::$directionRepresentation[$direction];
    }
    
    /**
     * Returns inverse direction for passed direction.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Direction $direction
     * @return string
     */
    public static function getInverseDirection($direction){
        switch($direction){
            case Direction::UP: return Direction::DOWN;
            case Direction::DOWN: return Direction::UP;
            case Direction::LEFT: return Direction::RIGHT;
            case Direction::RIGHT: return Direction::LEFT;
            case Direction::UPLEFT: return Direction::DOWNRIGHT;
            case Direction::UPRIGHT: return Direction::DOWNLEFT;
            case Direction::DOWNRIGHT: return Direction::UPLEFT;
            case Direction::DOWNLEFT: return Direction::UPRIGHT;
            default:
                die("Oops! INVALID DIRECTION '$direction'");
        }
    }
}