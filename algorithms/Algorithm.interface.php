<?php
/**
 * Defines basic Interface for Algorithms so we can use any one with PathFinder.
 * 
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
interface Algorithm {
    /**
     * Construct algorithm adding Source node to open list.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Map $map
     */
    public function __construct($map);
    
    /**
     * Finds the shortest path between to points on a given Map.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param Map $map
     * @return Path
     */
    public function find();
    
    /**
     * Returns start time in microseconds.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return int
     */
    public function getStart();
    
    /**
     * Returns end time in microseconds.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @return int
     */
    public function getEnd();
}