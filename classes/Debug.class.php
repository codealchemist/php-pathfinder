<?php
/**
 * Debug tools.
 *
 * @author Alberto Miranda <alberto.php@gmail.com>
 */
class Debug {
    /**
     * Indicates debug level.
     * Will determine which debug messages will get written or dropped.
     * 
     * @var int
     */
    public static $level = 1;
    
    /**
     * Writes passed message to standard output.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param string $message 
     */
    public static function out($message, $tag=null, $level=1){
        //filter message above current debug level
        if($level>self::$level) return false;
        
        if($tag!=null) $tag = "$tag: ";
        echo "$tag$message\n";
    }
    
    /**
     * Draw line.
     * Default used char is "-".
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     * @param char $char
     */
    public static function line($char='-'){
        $line = str_repeat($char, 80);
        self::out($line);
    }
    
    /**
     * Draws specified amount space (new lines).
     * Defaults to 1.
     * 
     * @author Alberto Miranda <alberto.php@gmail.com>
     */
    public static function space($amount=0){
        $space = str_repeat("\n", $amount);
        self::out($space);
    }
}