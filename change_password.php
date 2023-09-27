<?php
session_start();
include_once 'init.php';

$user = new User;

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();

        $validate->check($_POST, [
            'current_psw' => ['required' => true, 'min' => 6],
            'new_psw' => ['required' => true, 'min' => 6],
            're_new_psw' => ['required' => true, 'min' => 6, 'matches' => 'new_psw']
        ]);

        if ($validate->passed()) {
            if (password_verify(Input::get('current_psw'), $user->data()->password)) {
                $user->update(['password' => password_hash(Input::get('new_psw'), PASSWORD_DEFAULT)]);

                Session::flash('info', 'User successfully updated');
                Redirect::to('test.php');
            }
            echo 'Current password is invalid';

        } else {
            foreach ($validate->errors() as $error) {
                echo $error . '<br>';
            }
        }
    }
}
?>

<form action="" method="post">
    <div>
        <label for="current_psw">Current password: </label>
        <input id="current_psw" type="text" name="current_psw">
    </div>

    <div>
        <label for="new_psw">New password: </label>
        <input id="new_psw" type="text" name="new_psw">
    </div>

    <div>
        <label for="re_new_psw">New password again: </label>
        <input id="re_new_psw" type="text" name="re_new_psw">
    </div>

    <input type="hidden" name="token" value="<?= Token::generate() ?>">

    <div>
        <button>Save</button>
    </div>
</form>
