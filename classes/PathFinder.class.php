<?php
/**
 * The PathFinder uses an Algorithm to find the shortest path between two points 
 * in a given map.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class PathFinder {
    /**
     * The given Map the PathFinder will play with.
     * @var Map
     */
    private $map = null;
    
    /**
     * The Algorithm 
     * @var Algorithm 
     */
    private $algorithm = null;
    
    //--------------------------------------------------------------------------
    //GETTERS & SETTERS
    /**
     * Sets the Algorithm the Player will use to find the shortest path between
     * to poins on the current map.
     * Note this is not the algorythm name but the Algorithm object itself!
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Algorithm $algorithm 
     */
    public function setAlgorithm($algorithm){
        $this->algorithm = $algorithm;
    }
    
    /**
     * 
     * @return Algorithm
     */
    public function getAlgorithm() {
        return $this->algorithm;
    }
    
    /**
     * Sets the Map where PathFinder will find the shortest path between two
     * points. Those points will be defiend in the Map itself.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Map $map 
     */
    public function setMap($map) {
        $map = 
        $this->map = $map;
    }

    /**
     *
     * @return Map
     */
    public function getMap() {
        return $this->map;
    }
    //--------------------------------------------------------------------------
        
    /**
     * Constructs PathFinder with optional setting of Map and Algorithms.
     * They can be set later as well using the corresponding setters.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function __construct($map, $algorithm) {
        $this->loadMap($map);
        $this->loadAlgorithm($algorithm);
    }
    
    /**
     * Loads a Map from file.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $mapName 
     */
    public function loadMap($mapName){
        $this->setMap(new Map($mapName));
    }
    
    /**
     * Loads Algorithm by name.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $algorithmName 
     */
    public function loadAlgorithm($algorithmName){
        $algorithmsDir = Config::$algorithmsDir;
        $algorithmSufix = Config::$algorithmSufix;
        $algorithmFile = "$algorithmsDir/$algorithmName.$algorithmSufix";
        if(!file_exists($algorithmFile)) die("Oops! ALGORITHM NOT EXISTS '$algorithmFile'\n");
        
        require_once $algorithmFile;
        if(!class_exists($algorithmName)) die("Oops! Algorithm class should be named as '$algorithmName'");
        $this->algorithm = new $algorithmName($this->map);
    }
    
    /**
     * Uses choosen Algorithm to find the shortest path between the two points.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return Path
     */
    public function find(){
        $path = $this->getAlgorithm()->find();
        if(!$path) die("\nOops... PATH NOT FOUND!\nAre you sure this map is solvable?\n\n");
        $this->draw($path); //we found it! we draw it!
    }
    
    /**
     * Draws given Path on current Map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Path $path
     */
    public function draw(Path $path){
        $from = $this->map->source->getKey();
        $to = $this->map->destination->getKey();
        $mapName = $this->map->mapName;
        Debug::space();
        Debug::line("|");
        $elapsed = $this->algorithm->getEnd() - $this->algorithm->getStart();
        Debug::out("PATH from $from to $to on MAP '$mapName' found in $elapsed sec.", __METHOD__);
        
        /* @var $node Node */
        foreach($path->nodes as $node){
            if($node->parentDirection!=null){
                $node->parentDirection = Config::getInverseDirection($node->parentDirection); //TODO: use Helper class!
            }
            Debug::out("-> NODE {$node->getKey()} / {$node->parentDirection}", __METHOD__);
        }
        
        //draw path on map
        for($y = 1; $y <= $this->map->height; $y++){
            for($x = 1; $x <= $this->map->width; $x++){
                $node = $this->map->getNode($x, $y);
                $nodeRepresentation = Config::getNodeRepresentation($node->type);
                
                //overwrite node representation for path nodes
                if(
                    array_key_exists($node->getKey(), $path->nodes) && 
                    $node->type != NodeType::SOURCE &&
                    $node->type != NodeType::DESTINATION
                ){
                    $node = $path->nodes[$node->getKey()];
                    $nodeRepresentation = Config::getDirectionRepresentation($node->parentDirection);
                }
                echo "$nodeRepresentation";
            }
            echo "\n";
        }
        Debug::line("|");
        Debug::space(3);
    }
}