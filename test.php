<?php
session_start();
include_once 'init.php';

echo Session::flash('loginSuccess') . "<br>";
echo Session::flash('info') . "<br>";
$user = new User;

if ($user->isLogged()) {
    echo "Hi, <a href='profile.php'>{$user->data()->name}</a><br>";
    echo "<a href='change_password.php'>Change password</a><br>";
    echo "<a href='logout.php'>Logout</a><br>";

    if ($user->hasPermissions('admin')) {
        echo 'You are admin';
    }

} else {
    echo "<a href='login.php'>Login</a><br><a href='register.php'>Register</a>";
}

