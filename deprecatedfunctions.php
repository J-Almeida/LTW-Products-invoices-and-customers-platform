<?php
if ( false === function_exists('lcfirst') ) {
    function lcfirst( $str )
    { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
}

if ( get_magic_quotes_gpc () )
{
    if(!function_exists("traverse")){
        function traverse ( &$arr )
        {
            if ( !is_array ( $arr ) )
                return;

            foreach ( $arr as $key => $val )
                is_array ( $arr[$key] ) ? traverse ( $arr[$key] ) : ( $arr[$key] = stripslashes ( $arr[$key] ) );
        }
    }
    $gpc = array ( &$_GET, &$_POST, &$_COOKIE );
    traverse($gpc);
}

// seems like the only way around to using DateTime::createFromFormat in PHP 5.2
// http://stackoverflow.com/questions/5399075/php-datetimecreatefromformat-in-5-2
function createFromFormat( $dformat, $dvalue )
{
    $schedule = $dvalue;
    $schedule_format = str_replace(array('Y','m','d', 'H', 'i','a'),array('%Y','%m','%d', '%I', '%M', '%p' ) ,$dformat);
    // %Y, %m and %d correspond to date()'s Y m and d.
    // %I corresponds to H, %M to i and %p to a
    $ugly = strptime($schedule, $schedule_format);
    $ymd = sprintf(
    // This is a format string that takes six total decimal
    // arguments, then left-pads them with zeros to either
    // 4 or 2 characters, as needed
        '%04d-%02d-%02d %02d:%02d:%02d',
        $ugly['tm_year'] + 1900,  // This will be "111", so we need to add 1900.
        $ugly['tm_mon'] + 1,      // This will be the month minus one, so we add one.
        $ugly['tm_mday'],
        $ugly['tm_hour'],
        $ugly['tm_min'],
        $ugly['tm_sec']
    );
    $new_schedule = new DateTime($ymd);

    return $new_schedule;
}