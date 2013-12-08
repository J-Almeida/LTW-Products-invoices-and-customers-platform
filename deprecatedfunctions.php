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