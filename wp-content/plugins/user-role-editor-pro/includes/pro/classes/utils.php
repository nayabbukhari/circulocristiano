<?php
/**
 * Project: User Role Editor plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * Some parts of code were written by Janis Elsts (Broken Link Checker plugin)
 * License: GPL v2+
 * 
 * General useful stuff
 */
class URE_Utils {

    private static $execution_start_time = 0.0;
    
    /**
     * Get the server's load averages.   
     * Returns an array with three samples - the 1 minute avg, the 5 minute avg, and the 15 minute avg.
     *
     * @param integer $cache How long the load averages may be cached, in seconds. Set to 0 to get maximally up-to-date data.
     * @return array|null Array, or NULL if retrieving load data is impossible (e.g. when running on a Windows box). 
     */
    public static function get_server_load($cache = 5) {
        static $cached_load = null;
        static $cached_when = 0;

        if (!empty($cache) && ((time() - $cached_when) <= $cache)) {
            return $cached_load;
        }

        $load = null;

        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
        } else {
            $loadavg_file = '/proc/loadavg';
            if (@is_readable($loadavg_file)) {
                $load = explode(' ', file_get_contents($loadavg_file));
                $load = array_map('floatval', $load);
            }
        }

        $cached_load = $load;
        $cached_when = time();
        
        return $load;
        
    }
    // end of get_server_load()
    
    public static function microtime_float()	{
        list($usec, $sec) = explode(" ", microtime());
        
        return ((float)$usec + (float)$sec);
    }
    // end of microtime_float()
    
    
   /**
     * Check if server is currently too overloaded to run the link checker.
     *
     * @return bool
     */
    public static function server_too_busy() {        

        $loads = self::get_server_load();
        if (empty($loads)) {
            return false;
        }
        $one_minute = floatval(reset($loads));

        return $one_minute > 0.70;
    }
    // end of server_too_busy()

    
    public static function start_timer(){
        
        self::$execution_start_time = self::microtime_float();
        
    }
    // end of start_timer()
    
    
    public static function get_execution_time() {
        
        $execution_time = self::microtime_float() - self::$execution_start_time;
        
        return $execution_time;
    }
    // end of get_execution_time()
    
    
    /**
     * Sleep long enough to maintain the required $ratio between $elapsed_time and total runtime.
     *
     * For example, if $ratio is 0.25 and $elapsed_time is 1 second, this method will sleep for 3 seconds.
     * Total runtime = 1 + 3 = 4, ratio = 1 / 4 = 0.25.
     *
     * @param float $elapsed_time
     * @param float $ratio
     */
    public static function sleep_to_maintain_ratio($ratio) {
        if (($ratio<=0) || ($ratio>1)) {
            return;
        }
        $sleep_time = self::get_execution_time() * ((1 / $ratio) - 1);
        if ($sleep_time > 0.0001) {
            usleep($sleep_time * 1000000);
        }
    }
    // end of sleep_to_maintain_ratio()

    
    /**
     * Return 1st integer from the string, if it follows just after the key. Spaces are ignored;
     * $key - key substring after which we should extract the integer     
     * @param string $key - key substring after which we should extract the integer
     * @param type $heystack - string from which we should extract the integer
     * @return integer 
     */     
    public static function get_int_after_key($key, $heystack) {
       
        $key_pos = strpos($heystack, $key);
        if ($key_pos===false) {
            return 0;
        }
        
        $key_pos += strlen($key);        
        $length = strlen($heystack);
        $str = '';
        while($key_pos<$length) {
            $alpha = substr($heystack, $key_pos, 1);
            if ($alpha==' ') {
                continue;
            }
            if (!ctype_digit($alpha)) {
                break;
            }
            $str .= $alpha;
            $key_pos++;
        }
        
        $post_id = (int) $str;
        
        return $post_id;
    }
    // end of get_int_after_key()
}
// end of URE_Utils class