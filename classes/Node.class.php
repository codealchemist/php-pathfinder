<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Node
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Node {
    /**
     * @var Node 
     */
    public $parent = null;
    public $parentDirection = null;
    public $costToSource = 0;
    public $costToDestination = 0;
    public $totalCost = 0;
    public $x = null;
    public $y = null;
    public $type = null;
    
    public function __construct($x, $y, $type) {
        $this->x = $x;
        $this->y = $y;
        $this->type = $type;
    }
    
    /**
     * Returns Node's key used to identify it on nodes collections.
     * It's a concatenation of it's x and y coordinates: "$x$y"
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return string
     */
    public function getKey(){
        $key = "{$this->x}{$this->y}";
        return $key;
    }
}