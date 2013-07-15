<?php
/**
 * Implementation of the A* pathfinding algorithm.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Astar implements Algorithm {
    /**
     * Open list: nodes to explore
     * @var array Node
     */
    private $open = array();
    private $openByCost = array();
    
    /**
     * Closed list: nodes already explored
     * @var array Node
     */
    private $closed = array();
    
    /**
     * Used to draw algorithm current analysis iteraion
     * @var array Node
     */
    private $currentSuroundingNodes = array();
    
    /**
     * Used to draw algorithm current analysis iteraion
     * @var Node 
     */
    private $currentNode = null;
    
    /**
     * Used to draw algorithm current analysis iteraion and to limit iterations;
     * we also don't wan't to iterate forever in case something went wrong xD 
     * @var int
     */
    private $iteration = 0;
    
    /*
     * Used to calculate required time to solve
     */
    private $start = 0;
    private $end = 0;
    
    /**
     * Map where to find shortest path form source to destination
     * @var Map 
     */
    private $map = null;
    
    /**
     * Construct algorithm adding Source node to open list.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Map $map
     */
    public function __construct($map) {
        $this->map = $map;
        
        //add Source to open list
        $this->open[$map->source->getKey()] = $map->source;
    }
    
    /**
     * Costs per movement direction.
     * Stright costs 10 and diagonal 14.
     * It's an integer aprox. of the square root of 2, which is the distance
     * for diagonal movement; avoiding decimals uses less CPU.
     * @var type 
     */
    private $directionsCostMap = array(
        Direction::UP           => 10,
        Direction::DOWN         => 10,
        Direction::LEFT         => 10,
        Direction::RIGHT        => 10,
        Direction::UPLEFT       => 14,
        Direction::UPRIGHT      => 14,
        Direction::DOWNRIGHT    => 14,
        Direction::DOWNLEFT     => 14
    );
    
    /**
     * Finds the shortest path between to points on a given Map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com> 
     * @return Path
     */
    public function find() {
        if($this->iteration==0) $this->start = microtime();
        ++$this->iteration;
        if($this->iteration > Config::$maxIterations) die("MAX ITERATIONS REACHED! I did something wrong or the map is bigger than the allowed size!\n");
        
        //clear surounding nodes for current node; used to DRAW current iteration
        $this->currentSuroundingNodes = array();
        
        //if no node was specified it's the first iteration, so we should start
        //with source node
        if($this->currentNode==null) $this->currentNode = $this->map->source;
        Debug::line();
        Debug::out("- ITERATION: {$this->iteration}", __METHOD__);
        Debug::out("Current node: {$this->currentNode->getKey()}", __METHOD__);

        //----------------------------------------------------------------------
        //check if DESTINATION WAS REACHED
        if($this->destinationReached()) return $this->getPath($this->currentNode);
        //----------------------------------------------------------------------
        
        //move current node to closed list
        $this->moveNodeToClosedList($this->currentNode);
        
        //find surounding nodes for current node and update open list with them
        $this->updateSuroundingNodes($this->currentNode);
        $this->drawSuroundingNodes(); //DEBUG
        
        //recurse finding path to destination one surounding node at a time 
        //take the lowest cost node from nodes on open list
        for($i=0; $i<count($this->open); ++$i){
            //------------------------------------------------------------------
            //get CHEAPER node from OPEN list
            $this->currentNode = $this->getCheaperNode();
            //------------------------------------------------------------------
            
            $path = $this->find(); //recurse
            if($path){
                $this->end = microtime();
                return $path;
            }
        }
        //Debug::out("END.", __METHOD__);
    }
    
    /**
     * Moves passed Node to closed list and removes it from open list.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node
     */
    private function moveNodeToClosedList(Node $node){
        unset($this->open[$node->getKey()]);
        $this->closed[$node->getKey()] = $node;
    }
    
    /**
     * Returns true if Destination was reached.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return boolean
     */
    private function destinationReached(){
        if(empty($this->open) && empty($this->closed)) die("Oops! Seems like there were no nodes to explore! Ensure having a Source node to add to open list first.");
        if(empty($this->open) && !empty($this->closed)){
            die("Oops! Unable to find a path from Source to Destination.\n" . 
                "SOURCE: {$this->map->source->getKey()}\n" .
                "DESTINATION: {$this->map->destination->getKey()}"
            );
        }
        
        //check if we REACHED DESTINATION NODE
        if($this->currentNode->getKey() == $this->map->destination->getKey()){
            Debug::out("--> YAY! REACHED DESTINATION! <--", __METHOD__);
            return true;
        }
        return false;
    }
    
    /**
     * Returns "cheaper" node from Open list.
     * This is the node with lowest total cost.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return Node
     */
    private function getCheaperNode(){
        /* @var $node Node */
        $cheaper = null;
        foreach($this->open as $key => $node){
            if($cheaper==null){
                $cheaper = $node;
                continue;
            }
            if($node->totalCost < $cheaper->totalCost) $cheaper = $node;
        }
        return $cheaper;
    }
    
    /**
     * Updates open list with nodes surounding passed node.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com> 
     * @param Node $node 
     * @return array Node
     */
    private function updateSuroundingNodes(Node $node){
        $parent = 'none';
        if($node->parent!=null) $parent = $node->parent->getKey();
        Debug::out("CURRENT NODE: {$node->getKey()} / parent NODE: $parent", __METHOD__);
        
        //try to find a surounding node in each available direction
        foreach($this->directionsCostMap as $direction => $cost){
            $suroundingNode = $this->getSuroundingNode($node, $direction);
            if(!$suroundingNode) continue; //if false it's not no the map
            
            Debug::out(">> SUROUNDING NODE: {$suroundingNode->getKey()} / type: {$suroundingNode->type} / @$direction", __METHOD__);
            $key = $suroundingNode->getKey();
            if(!$this->isWalkable($suroundingNode)) continue; //do nothing with non free nodes, we can't go over there!
            if(array_key_exists($key, $this->closed)) continue; //do nothing with closed nodes
            
            //first get cost to source so we can check if we found a better path
            //for nodes on open list or not; if node is on open list and the path
            //is NOT better we are done with this node
            $costToSource = $this->getCostToSource($node, $suroundingNode, $direction);
            
            //if on open list we need to check if it's better reach it thru current node
            //in which case current node will become it's new parent
            if(array_key_exists($key, $this->open)){
                //get existing node from open list to get its calculated costs
                $suroundingNode = $this->open[$key];
                Debug::out("+ NODE $key found on OPEN list; parent: {$node->parent->getKey()}", __METHOD__);
                
                //get cost of moving to surounding node thru current node
                Debug::out("+ NODE $key: new path cost: $costToSource / old path cost: {$suroundingNode->costToSource}", __METHOD__);
                if($suroundingNode->costToSource < $costToSource) continue; //ok, not so good to go thru current node
                
                Debug::out("Greatto Scotto! Found better path thru NODE {$suroundingNode->getKey()}", __METHOD__);
            }
            
            //if not exist on open or closed lists it's a new unexplored node
            //calculate the cost of moving thru it and set current node as parent
            $costToDestination = $this->getCostToDestination($suroundingNode);
            $totalCost = $costToSource + $costToDestination;
            $suroundingNode->costToSource = $costToSource;
            $suroundingNode->costToDestination = $costToDestination;
            $suroundingNode->totalCost = $totalCost;
            $suroundingNode->parent = $node;
            $suroundingNode->parentDirection = Config::getInverseDirection($direction);
            $this->currentSuroundingNodes[$key] = $suroundingNode; //to draw 
            
            //add node to open list
            $this->open[$key] = $suroundingNode;
        }
    }
    
    /**
     * Returns true if passed node is walkable.
     * False if not.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node 
     * @return boolean
     */
    private function isWalkable($node){
        if(in_array($node->type, Config::$walkableNodeTypes)) return true;
        return false;
    }
    
    /**
     * Returns surounding node at specified direction or false if not available.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node
     * @param Direction $direction 
     */
    private function getSuroundingNode(Node $node, $direction){
        switch($direction){
            case Direction::UP:
                $y = $node->y - 1;
                $x = $node->x;
                break;
            
            case Direction::DOWN:
                $y = $node->y + 1;
                $x = $node->x;
                break;
            
            case Direction::LEFT:
                $y = $node->y;
                $x = $node->x - 1;
                break;
            
            case Direction::RIGHT:
                $y = $node->y;
                $x = $node->x + 1;
                break;
            
            case Direction::UPLEFT:
                $y = $node->y - 1;
                $x = $node->x - 1;
                break;
            
            case Direction::UPRIGHT:
                $y = $node->y - 1;
                $x = $node->x + 1;
                break;
            
            case Direction::DOWNRIGHT:
                $y = $node->y + 1;
                $x = $node->x + 1;
                break;
            
            case Direction::DOWNLEFT:
                $y = $node->y + 1;
                $x = $node->x - 1;
                break;
            
            default:
                die("Oops! INVALID DIRECTION '$direction'");
        }
        
        //VALIDATE COORDINATES for surounding node and return it
        //if node does not exist no Map it will be returned as false
        if($x<0 or $y<0) return false; //negative is outsite the map
        $suroundingNode = $this->map->getNode($x, $y);
        return $suroundingNode;
    }
    
    /**
     * Returns path from current node to source node.
     * Should be used to get path when destination node has been found.
     * Iteratively gets parent until last one (source) is reached, returning
     * an array of those nodes, which are the actual path.
     * The param Node is the last node found.
     * All chained parent nodes form the path. 
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node
     * @return array Node
     */
    private function getPath($node){
        return new Path($node);
    }
    
    /**
     * Returns cost of moving from source node to current node.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node 
     * @param Node $suroundingNode 
     * @param Direction $direction
     * @return int
     */
    public function getCostToSource(Node $node, Node $suroundingNode, $direction){
        if(!array_key_exists($direction, $this->directionsCostMap)) die("Oops! Direction '$direction' not found in 'directionsCostMap'");
        
        //if current node has no parent just return current move cost
        if($suroundingNode->parent==null) return $node->costToSource + $this->directionsCostMap[$direction];
        
        //passed node has a parent so we need to add current movement cost to
        //its existing one
        $cost = $node->costToSource + $suroundingNode->parent->costToSource + $this->directionsCostMap[$direction];
        return $cost;
    }
    
    /**
     * Returns estimated cost of moving from passed node to destination by
     * calculating the cost of moving in straight line if possible.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node 
     * @return int
     */
    public function getCostToDestination(Node $node){
        $destination = $this->map->destination;
        
        $deltaX = abs($destination->x - $node->x);
        $deltaY = abs($destination->y - $node->y);
        $delta = $deltaX + $deltaY;
        $cost = 0;
        while($delta>0){
            //diagonal movement
            if($deltaX > 0 && $deltaY > 0){
                $cost += $this->directionsCostMap[Direction::UPRIGHT]; //any diagonal movement will do
                --$deltaX;
                --$deltaY;
                $delta = $delta-2; //we are two points closer to destination, one for each axis
                continue;
            }
            
            //straight movement
            $cost += $this->directionsCostMap[Direction::UP]; //any straight movement will do
            
            //X move
            if($deltaX > 0){
                --$deltaX;
                --$delta;
                continue;
            }
            
            //Y move
            if($deltaY > 0){
                --$deltaY;
                --$delta;
                continue;
            }
        }
        return $cost;
    }
    
    /**
     * 
     * @param type $node 
     */
    public function getTotalCost(Node $node){
        
    }
    
    /**
     * Draws surounding nodes on current Map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function drawSuroundingNodes(){
        $nodes = $this->currentSuroundingNodes;
        $count = count($nodes);
        Debug::out("SUROUNDING NODES for node {$this->currentNode->getKey()} ($count):", __METHOD__);
        foreach($nodes as $node){
            Debug::out("+ NODE {$node->getKey()} >> F: {$node->totalCost} / G: {$node->costToSource} / H: {$node->costToDestination}", __METHOD__);
        }
        
        //draw surounding nodes on map
        for($y = 1; $y <= $this->map->height; $y++){
            for($x = 1; $x <= $this->map->width; $x++){
                $node = $this->map->getNode($x, $y);
                $nodeRepresentation = Config::getNodeRepresentation($node->type);
                
                //if it's a surounding node overwrite its representation
                if(array_key_exists($node->getKey(), $nodes)){
                    $nodeRepresentation = Config::getDirectionRepresentation($node->parentDirection);
                }
                
                //overwrite node representation for current node
                if($node->getKey() == $this->currentNode->getKey()){
                    $nodeRepresentation = Config::getNodeRepresentation(NodeType::CURRENT);
                }
                echo "$nodeRepresentation";
            }
            echo "\n";
        }
    }
    
    /**
     * Returns start time in microseconds.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return int
     */
    public function getStart(){
        return $this->start;
    }
    
    /**
     * Returns end time in microseconds.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return int
     */
    public function getEnd(){
        return $this->end;
    }
}