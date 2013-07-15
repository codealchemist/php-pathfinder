<?php
/*
 * Runs PathFinder on passed Map.
 * 
 * @author Alberto Miranda <alberto.php@gmail.com>
 */

require_once 'inc/default.inc.php';

//clear and show title
system("clear");
echo $title;

//------------------------------------------------------------------------------
//check for received params
if(count($argv)<3) die("--> USAGE: php run.php [map] [algorithm]\n\n\n");
//------------------------------------------------------------------------------

//recieve shell params
$map = $argv[1];
$algorithm = $argv[2];
$pathFinder = new PathFinder($map, $algorithm);

//user input
$mapRepresentation = $pathFinder->getMap()->getRepresentation();
$mapName = $pathFinder->getMap()->mapName;
echo <<< EOF
Selected Map '$mapName':
$mapRepresentation

Type 'y' followed by enter key to continue
solving map '$map' using algorithm '$algorithm': 
EOF;
$yes = fgets(STDIN);
$yes = trim($yes);
if($yes != 'y') die("BYE!\n\n");

//FIND PATH
$path = $pathFinder->find();