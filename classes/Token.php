<?php
include_once 'Session.php';
include_once 'Config.php';

class Token
{
    public static function generate()
    {
        return Session::put(Config::get('session.token_name'), md5(uniqid()));
    }

    public static function check($token)
    {
        $tokenName = Config::get('session.token_name');

        if (Session::exists($tokenName) && $token == Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }

        return false;
    }
}
