<?php
/**
 * In this file you'll find all Enums used by the app.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */

/**
 * Nodes enum
 */
final class NodeType {
    const FREE          = 'free';
    const WALL          = 'wall';
    const SOURCE        = 'source';
    const DESTINATION   = 'destination';
    const INVALID       = 'invalid';
    const CURRENT       = 'current';
}

/**
 * Movement directions enum.
 */
final class Direction {
    const UP        = 'up';
    const DOWN      = 'down';
    const LEFT      = 'left';
    const RIGHT     = 'right';
    const UPLEFT    = 'upleft';
    const UPRIGHT   = 'upright';
    const DOWNRIGHT = 'downright';
    const DOWNLEFT  = 'downleft';
}