<?php
namespace StumpJsCompiler\Exception;


/**
 * Manufactors exeptions
 *
 * @author barringtonhenry
 *        
 */
final class Factory
{
    const UNKOWN_EXEC = 'unknownexecutable';
    const INVALID_LOC = 'invalidlocation';
    
    protected static $messages = array(
                self::UNKOWN_EXEC   => 'Executable is not known, place name in configs',
                self::INVALID_LOC   => 'Bin location not found'
            );
    
    public static function throwInvalidLocation($message = null)
    {
        throw new InvalidLocationException(
                ($message!==null) ? $message : self::getTheMessage(self::INVALID_LOC)
                );   
    }
    
    public static function throwUnknownExecutable($message = null)
    {
        throw new UnknownExecutableException(
                ($message!==null) ? $message : self::getTheMessage(self::UNKOWN_EXEC)
                );
    }
    
    /**
     * 
     * @param string $key
     * @return string
     */
    public static function getTheMessage($key)
    {
        return self::$messages[$key];
    }
}

?>