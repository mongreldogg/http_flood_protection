<?php

/*
 * PHP flood protection
 * Created by: mongreldogg (jimborecard@gmail.com)
 * You can do anything you want with this
 */

define('ACCESS_TOKEN_SALT', 'somethingICantRemember');

class FloodProtection
{
    public function __construct()
    {
        $url = $_SERVER['REQUEST_URI'];
        $referrer = $_SERVER['HTTP_REFERRER'];
        $domain = $_SERVER['SERVER_NAME'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $cookie = $_COOKIE['__access'];
        $verify = md5(ACCESS_TOKEN_SALT.self::GetClientIP().$browser.$_SERVER['SERVER_NAME']);
        if (sizeof(get_included_files()) == 1 && $cookie == $verify) {
            //DO NUFFIN, CONTINUE
        } else {
            if ($cookie != $verify && !self::IsBot()) {
                self::GenerateBrowserCheck($domain, $verify, $browser, $referrer);
            }
        }
    }

    protected static function GenerateBrowserCheck($domain, $cookie, $browser, $referrer)
    {
        ?>

<html>
<head>
    <title>Checking your browser accessing <?=$domain; ?></title>
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

    protected static function GetClientIP()
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

    protected static function IsBot()
    {
        return
            isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
          ;
    }
}

$protection = new FloodProtection();
