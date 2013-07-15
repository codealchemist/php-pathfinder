php-pathfinder
==============

"Pathfinding or pathing refers to the plotting, by a computer application, of the shortest route between two points".
[More about pathfinding on Wikipedia](http://en.wikipedia.org/wiki/Path_finding)


      ,_   __,  -/- /_       /)  .  ,__,   __/   _   ,_
     _/_)_(_/(__/__/ (_    _//__/__/ / (__(_/(__(/__/ (_
     /                    _/
    /                     /)
                          `             font: JS Cursive
    
    @author Alberto Miranda <alberto.php@gmail.com>

Description:

PathFinder is a PHP app that lets you configure
simple maps with empty nodes, walls, a starting
point and a destination point so it can find the
shortest path between those two points on the
given map.
It can implement multiple solving algorithms and
any amount of maps you want to and its really
simple to use it!

    Map definitions:
        . = free
        ❚ = wall
        0 = origin
        1 = destination


**Built-in MAPS:**
- 20x6
- 50x20
- default
- difficult
- difficult2
- impossible

**Built-in ALGORITHMS:**
- Astar

If you want to add more algorithms add them in the "algorithms" folder implementing the "Algorithm.interface.php" interface.



**USAGE:** 
    
    php run.php [map] [algorithm]
    
*Have fun!*
