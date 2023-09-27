<?php
session_start();
include_once 'classes/Database.php';
include_once 'classes/Config.php';
include_once 'classes/Session.php';
include_once 'classes/Input.php';
include_once 'classes/Token.php';
include_once 'classes/Validate.php';
include_once 'classes/User.php';
include_once 'classes/Redirect.php';
include_once 'classes/Cookie.php';

$GLOBALS['config'] = [
    'mysql' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'objects',
        'user' => 'root',
        'password' => ''
    ],
    'session' => [
        'token_name' => 'token',
        'user_session' => 'user'
    ],
    'cookie' => [
        'cookie_name' => 'hash',
        'cookie_expiry' => '604800'
    ]
];

if (Cookie::exists(Config::get('cookie.cookie_name')) && !Session::exists(Config::get('session.user_session'))) {
    $hash = Cookie::get(Config::get('cookie.cookie_name'));
    $hasUserWithHash = Database::getInstance()->get('user_cookies', ['hash', '=', $hash]);

    if ($hasUserWithHash->count()) {
        $user = new User($hasUserWithHash->first()->user_id);
        $user->login();
    }
}


