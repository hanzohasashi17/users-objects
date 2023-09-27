<?php
include_once 'init.php';

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();

        $validation = $validate->check($_POST, [
            'name' => [
                'required' => true,
                'min' => 3,
                'max' => 16,
            ],
            'email' => [
                'required' => true,
                'min' => 6,
                'unique' => 'users',
                'email' => 'true',
            ],
            'password' => [
                'required' => true,
                'min' => 3
            ],
            'repeat_password' => [
                'required' => true,
                'matches' => 'password'
            ]
        ]);

        if ($validation->passed()) {
            $user = new User;
            $user->create([
                'name' => Input::get('name'),
                'email' => Input::get('email'),
                'password' => password_hash(Input::get('password'), PASSWORD_DEFAULT),
            ]);
            Session::flash('success', 'User successfully registered');
            Redirect::to('login.php');
        } else {
            foreach ($validation->errors() as $error) {
                echo $error . '<br>';
            }
        }
    }
}
?>

<form action="" method="post">
    <div class="field">
        <label for="name">Usename</label>
        <label>
            <input type="text" name="name" value="<?= Input::get('name') ?>">
        </label>
    </div>

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
        <label for="repeat_password">Repeat the password</label>
        <label>
            <input type="text" name="repeat_password" value="<?= Input::get('repeat_password') ?>">
        </label>
    </div>

    <input type="hidden" name="token" value="<?= Token::generate() ?>">

    <div class="field">
        <button type="submit">Register</button>
    </div>
</form>
