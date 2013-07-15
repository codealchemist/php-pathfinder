<?php
/**
 * Default app includes and title set.
 * 
 * @author Alberto Miranda <alberto.php@gmail.com>
 */

//------------------------------------------------------------------------------
//load all CLASSES at once
$classSufix = 'class.php';
$classDir = dirname(__FILE__) . '/../classes';
$dirContent = scandir($classDir); //using scandir to get files and dirs ordered alphabethically
foreach($dirContent as $file){
    //skip unwanted files and dirs
    $omit = array('.', '..', '.svn');
    if(in_array($file, $omit) or is_dir($file)) continue;

    preg_match("/^(.*?)\.$classSufix/", $file, $matches);
    if(empty($matches)) continue; //do nothing with non matching files
    $key = $matches[1];
    
    $classFile = "$classDir/$file";
    require_once $classFile;
}

//init config; this will override config with selected config file in /config.
Config::init();

//load interfaces
require_once dirname(__FILE__) . '/../algorithms/Algorithm.interface.php';
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
//instantiate Config and get available maps and algorithms
$spacer = "\n    - ";
$maps = implode("$spacer", array_keys(Config::getMaps()));
$algorithms = implode($spacer, array_keys(Config::getAlgorithms()));
//------------------------------------------------------------------------------


//set title
$free = Config::getNodeRepresentation(NodeType::FREE);
$wall = Config::getNodeRepresentation(NodeType::WALL);
$origin = Config::getNodeRepresentation(NodeType::SOURCE);
$destination = Config::getNodeRepresentation(NodeType::DESTINATION);
$title = <<< EOF
                                                        
  ,_   __,  -/- /_       /)  .  ,__,   __/   _   ,_ 
 _/_)_(_/(__/__/ (_    _//__/__/ / (__(_/(__(/__/ (_
 /                    _/                            
/                     /)                            
                      `             font: JS Cursive
                      
@author Alberto Miranda <alberto.php@gmail.com>

Description:
    PathFinder is a PHP app that lets you configure
    simple maps with empty nodes, walls a starting
    point and a destination point so it can find the
    shortest path between those two points on the
    given map.
    It can implement multiple solving algorithms and
    any amount of maps you want to and its really
    simple to use it!
        
    Map definitions:
        $free = free
        $wall = wall
        $origin = origin
        $destination = destination
    
        
    Have fun!

----------------------------------------------------
MAPS: 
    - $maps
    
ALGORITHMS: 
    - $algorithms
----------------------------------------------------


EOF;
