<?php
session_start();
include_once 'init.php';

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();

        $validate->check($_POST, [
            'email' => ['required' => true, 'email' => true],
            'password' => ['required' => true],
        ]);

        if ($validate->passed()) {
            $user = new User;
            $remember = Input::get('remember_me') === 'on';

            $login = $user->login(Input::get('email'), Input::get('password'), $remember);

            if ($login) {
                Session::flash('loginSuccess', 'User successfully logged');
                Redirect::to('test.php');
            } else {
                echo 'Something went wrong';
            }
        } else {
            foreach ($validate->errors() as $error) {
                echo $error . '<br>';
            }
        }
    }
}



?>

<?= Session::flash('success') ?>
<form action="" method="post">
    <div class="field">
        <label for="email">Email</label>
        <label>
            <input type="text" name="email" value="<?= Input::get('email') ?>">
        </label>
    </div>

    <div class="field">
        <label for="password">Password</label>
        <label>
            <input type="text" name="password" value="<?= Input::get('password') ?>">
        </label>
    </div>

    <div class="field">
        <label>
            <input type="checkbox" name="remember_me">
        </label>
        <label for="remember_me">Remember me</label>
    </div>

    <input type="hidden" name="token" value="<?= Token::generate() ?>">

    <div class="field">
        <button type="submit">Sign</button>
    </div>
</form>
