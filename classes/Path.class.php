<?php
/**
 * A Path is a collection of Nodes between to given points in a Map.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Path {
    /**
     * @var array Node
     */
    public $nodes = array();
    
    //--------------------------------------------------------------------------
    //GETTERS & SETTERS
    /**
     * Sets path from current node to source node.
     * Should be used to get path when destination node has been found.
     * Iteratively gets parent until last one (source) is reached, returning
     * an array of those nodes, which are the actual path.
     * The param Node is the last node found.
     * All chained parent nodes form the path. 
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Node $node
     */
    public function setNodes($node) {
        $iterations = 0;
        while($node != null){
            ++$iterations;
            if($iterations > Config::$maxIterations) die("Oops... I did something wrong on " . __METHOD__);
            
            $this->nodes[$node->getKey()] = $node;
            $node = $node->parent;
        }
        
        //invert path so it goes from Source to Destination
        $this->nodes = array_reverse($this->nodes, true);
    }
    //--------------------------------------------------------------------------
    
    /**
     * Constructs Path with given Nodes array.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function __construct($node) {
        $this->setNodes($node);
    }
    
    /**
     * Draws Path to standard output.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public function draw(){
        
    }
    
    /**
     * Returns current Path's length (amount of Nodes it has).
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return int
     */
    public function getLength(){
        return count($this->nodes);
    }
}