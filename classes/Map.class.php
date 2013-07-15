<?php
/**
 * The Map defines its dimensions and form and has the setup for origin,
 * destination and unwalkable areas.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Map {
    /**
     * @var array Node
     */
    public $nodes = null;
    
    /**
     * @var Node
     */
    public $source = null;
    
    /**
     * @var Node
     */
    public $destination = null;
    
    /**
     * @var int
     */
    public $width = null;
    
    /**
     * @var int
     */
    public $height = null;
    
    /**
     * Current Map name
     * @var string
     */
    public $mapName = '';
    
    /**
     * Current Map file
     * @var string 
     */
    public $mapFile = '';
    
    /**
     * Constructs Map loading map file data.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param type $mapFile 
     */
    public function __construct($mapName) {
        $this->mapName = $mapName;
        $mapsDir = Config::$mapsDir;
        $mapsSufix = Config::$mapSufix;
        $mapFile = "$mapsDir/$mapName.$mapsSufix";
        $this->mapFile = $mapFile;
        if(!file_exists($mapFile)) die("Oops! MAP FILE NOT EXISTS '$mapFile'\n");
        
        //make coordinates start at 1 so we can use coordinates as array keys
        //in a more consistent way
        $rows = file($mapFile);
        $y = 1;
        $nodes = array();
        foreach($rows as $rowString){
            $x = 1;
            $rowString = trim($rowString);
            $rowArray = str_split($rowString);
            foreach($rowArray as $char){
                $type = $this->getNodeType($char);
                $node = new Node($x, $y, $type);
                $nodes["$x$y"] = $node;
                if($type==NodeType::SOURCE) $this->source = $node;
                if($type==NodeType::DESTINATION) $this->destination = $node;
                $x++;
            }
            $y++;
        }
        $this->width = --$x;
        $this->height = --$y;
        $this->nodes = $nodes;
    }
    
    /**
     * Returns node type for current node char, as read from map file.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param type $char
     */
    private function getNodeType($char){
        switch($char){
            case '.': return NodeType::FREE;
            case '|': return NodeType::WALL;
            case '0': return NodeType::SOURCE;
            case '1': return NodeType::DESTINATION;
        }
        return NodeType::INVALID;
    }
    
    /**
     * Returns Node at requested coordinates or false if not available.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param int $x
     * @param int $y 
     * @return Node
     */
    public function getNode($x, $y){
        if(!array_key_exists("$x$y", $this->nodes)) return false;
        return $this->nodes["$x$y"];
    }
    
    /**
     * Returns string representation of current map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function getRepresentation(){
        return file_get_contents($this->mapFile);
    }
}