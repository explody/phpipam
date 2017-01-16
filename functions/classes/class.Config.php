<?php 

class ImmutableConfigException extends Exception { }
class IpamConfigNotFound extends Exception { }
class IpamEnvironmentNotFound extends Exception { }

/* 
 * This class is intended as a container for config data
 * It's a pseudo-singleton, so to speak, as it cannot be instantiated
 * outside itself and only the "top" instance will ever be returned,
 * but it may contain a hierarchy of many instances of itself.
 */

class MostlyImmutableConfig {
    
    // Define recursion limit
    private $maxdepth = 5;
    
    /* 
    | This array will contain the data stored in this psuedo-Singleton
    */
    private $properties = [];
    
    /* 
    | This will contain our Singleton instance
    */
    private static $instance;
    
    private function __construct($data, $level = 0) {
        
        // fairly dumb protection against infinite recursion
        // If $level count increase beyond $maxdepth, stop.
        // Your data structure will still be ugly.
        if ($level >= $this->maxdepth) {
            return;
        }
        
        // Process the data passed to init()
        foreach ($data as $k=>$v) {
            
            if (self::isIterable($v)) {
                // if the value is itself iterable, reimagine it as a MostlyImmutableConfig
                $this->properties[$k] = new MostlyImmutableConfig($v,$level+1);
            } else if (is_object($v)) {
                // We will not accept objects that are not explicitly array-like. Sorry.
                continue;
            } else {
                $this->properties[$k] = $v;
            }
        }
    }
    
    /* 
     * Detect arrays and array-like objects
     * 
     * @param mixed $var 
     *
     * @return bool 
     */ 
    private static function isIterable ($var) {
        if (is_array($var) || $var instanceof ArrayAccess  &&
                              $var instanceof Traversable  &&
                              $var instanceof Serializable &&
                              $var instanceof Countable) {
            return true;
        }
        return false;
    }
    
    /* 
     * Recursively merges two arrays. Unlike the bulitin array_merge_recursive, this behaves like array_merge
     * all the way down the hierarchy.
     * 
     * @param array $array1 
     * @param array $array2 
     *
     * @return array 
     */
    public static function config_merge_recursive($array1, $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            
            if (array_key_exists($key, $array1)) {
                if (is_array($value) && is_array($array1[$key])) {
                    $nval = self::config_merge_recursive($array1[$key], $value);
                } else {
                    $nval = $value;
                }
            } else {
                $nval = $value;
            }
            $merged[$key] = $nval;
        }
        
        return $merged;
    }
    
    /* 
     * If we've been intialized, return true. Otherwise, false.
     *
     * @return bool
     */
    public static function alive() {
        if (self::$instance){
          return true;
        }
        return false;
    }
    
    /* 
     * Initialize the internal instance with an iterable data structure
     * 
     * @param mixed $data 
     *
     * @return void
     */
    public static function init($data) {
        if (!self::$instance){
            self::$instance = new MostlyImmutableConfig($data);
        }
    }

    /* 
     * Return the config instance.
     *
     * @return MostlyImmutableConfig
     */
    public static function config() {
        return self::$instance;
    }
    
    /* 
     * Override the magic method. Fetch data from the properties array
     * 
     * @param mixed $name Name of the property to return 
     *
     * @return mixed
     */
    public function __get($name) {
        return (array_key_exists($name, $this->properties) ? $this->properties[$name] : null);
    }
    
    /* 
     * Override the magic method. __set is effectively disabled. 
     * Values may only be loaded during init(). Throw an exception if someone tries to write.
     * 
     * @param mixed $name Default parameter passed to __get. Ignored.
     * @param mixed $val Default parameter passed to __get. Ignored.
     *
     * @return mixed
     */
    public function __set($name, $val) {
        throw new ImmutableConfigException("Values may only be set during init()");
    }
    
    /* 
     * Return the hierarchy of properties as an array, for iterating.
     * 
     * @return array
     */
    public function as_array() {
        $a = [];
        foreach ($this->properties as $k=>$v) {
            if ($v instanceof MostlyImmutableConfig) {
                $a[$k] = $v->as_array();
            } else {
                $a[$k] = $v;
            }
        }
        return $a;
    }
    
}

// This is nothing more than a wrapper to use a nicer name in the app
class IpamConfig extends MostlyImmutableConfig { }

?>