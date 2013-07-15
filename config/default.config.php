<?php
/**
 * Default PathFinder config
 * @author Alberto Miranda <alberto.php@gmail.com>
 */

$config = array(
    'mapsDir' => 'maps',
    'algorithmsDir' => 'algorithms',
    'nodeRepresentations' => array(
        NodeType::FREE => '.',
        NodeType::WALL => '❚',
        NodeType::SOURCE => '0',
        NodeType::DESTINATION => '1',
        NodeType::CURRENT => '*'
    ),
    'directionRepresentation' => array(
        Direction::UP => '↑',
        Direction::DOWN => '↓',
        Direction::LEFT => '←',
        Direction::RIGHT => '→',
        Direction::UPLEFT => '\\',
        Direction::UPRIGHT => '/',
        Direction::DOWNRIGHT => '\\',
        Direction::DOWNLEFT => '/'
    )
);