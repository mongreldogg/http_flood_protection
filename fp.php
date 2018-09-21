<?php

/*
 * PHP flood protection
 * Created by: mongreldogg (jimborecard@gmail.com)
 * You can do anything you want with this
 */

define('ACCESS_TOKEN_SALT', 'somethingICantRemember');

class FloodProtection
{

    /**
     * Predefined IPs for which not to show this check
     */

    private static $bypassIPs = [
        '91.219.236.184',
        '77.109.141.170',
        '91.205.41.208',
        '94.242.216.60',
        '78.41.203.75',
        '127.0.0.1',
        '10.0.0.5'
    ];

    /**
     * Browser check initializer
     */

    public function __construct()
    {
        $url = $_SERVER['REQUEST_URI'];
        $domain = $_SERVER['SERVER_NAME'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        @$cookie = $_COOKIE['__access'];
        $clientIP = self::GetClientIP();
        $verify = md5(ACCESS_TOKEN_SALT.$clientIP.$browser.$_SERVER['SERVER_NAME']);
        $bypass = false;
        foreach(self::$bypassIPs as $ip){
            if($clientIP == $ip) {
                $bypass = true;
                break;
            }
        }
        if ($cookie != $verify && !self::IsBot() && $bypass == false) {
            self::GenerateBrowserCheck($domain, $verify, $browser, $url);
        }
    }

    /**
     * A content body for browser check script page
     */

    protected static function GenerateBrowserCheck($domain, $cookie, $browser, $referrer)
    {
        ?>

<html>
<head>
    <title>Checking your browser before accessing <?=$domain; ?></title>
</head>
<body>
    <script type="text/javascript">
        if(btoa(navigator.userAgent) 
            == '<?=base64_encode($browser); ?>'){
            document.cookie = '__access=<?=$cookie; ?>';
            setTimeout(function(){
                document.location.href = '<?=$referrer; ?>';
            }, 5000);
        } else {
            alert('Your browser sent a mismatched verification data. '+
                    'Please disable any browser extensions that might cause this issue to grant access to this website. '+
                    'Thank you!');
        }
    </script>
    Checking your browser accessing <?=$domain; ?>. You will be redirected to main page in a few seconds.
</body>
</html>

        <?php
        exit;
    }

    /**
     * A function to identify real client IP address. 
     * One of those environment variables should be passed by web server to PHP
     */

    public static function GetClientIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            }

            return $_SERVER['REMOTE_ADDR'];
        }

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        }

        if (getenv('HTTP_CLIENT_IP')) {
            return getenv('HTTP_CLIENT_IP');
        }

        return getenv('REMOTE_ADDR');
    }

    /**
     * Identifies a search bot / crawler by client header
     * UPD: perform additional checks since not that much of a secure way
     */

    protected static function IsBot()
    {
        return
            isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/aolbuild|baidu|bingbot|msnbot|bingpreview|duckduckgo|adsbot-google|googlebot|mediapartners-google|teoma|slurp|yandex/i', $_SERVER['HTTP_USER_AGENT'])
          ;
    }
}

$protection = new FloodProtection();
