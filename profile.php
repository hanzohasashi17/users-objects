<?php
session_start();
include_once 'init.php';

$user = new User;

if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $validate = new Validate();

        $validate->check($_POST, [
            'name' => ['required' => true, 'min' => 4, 'max' => 16]
        ]);

        if ($validate->passed()) {
            $user->update(['name' => Input::get('name')]);

            Session::flash('info', 'User successfully updated');
            Redirect::to('test.php');
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
        <label for="name">Edit name: </label>
        <input id="name" type="text" name="name" value="<?= $user->data()->name ?>">
    </div>

    <input type="hidden" name="token" value="<?= Token::generate() ?>">

    <div>
        <button>Save</button>
    </div>
</form>
