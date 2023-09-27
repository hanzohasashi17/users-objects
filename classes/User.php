<?php
include_once 'Database.php';
include_once 'Session.php';
class User
{
    private $db, $data, $sessionName, $cookieName, $isLogged;

    public function __construct($user = null)
    {
        $this->db = Database::getInstance();
        $this->sessionName = Config::get('session.user_session');
        $this->cookieName = Config::get('cookie.cookie_name');

        if (!$user) {
            if (Session::exists($this->sessionName)) {
                $user = Session::get($this->sessionName);

                if ($this->find($user)) {
                    $this->isLogged = true;
                }
            }
        } else {
            $this->find($user);
        }

    }

    public function create($fields = [])
    {
        $this->db->insert('users', $fields);
    }

    public function login($email = null, $password = null, $remember = false)
    {
        if (!$email && !$password && $this->exists()) {
            Session::put($this->sessionName, $this->data()->id);
        } else {
            $hasUser = $this->find($email);
            if ($hasUser) {
                if (password_verify($password, $this->data()->password)) {
                    Session::put($this->sessionName, $this->data()->id);

                    if ($remember) {
                        $hash = hash('sha256', uniqid());

                        $hashCheck = $this->db->get('user_cookies', ['user_id', '=', $this->data()->id]);

                        if (!$hashCheck->count()) {
                            $this->db->insert('user_cookies', [
                                'user_id' => $this->data()->id,
                                'hash' => $hash
                            ]);
                        } else {
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->cookieName, $hash, Config::get('cookie.cookie_expiry'));
                    }

                    return true;
                }
            }
        }

        return false;
    }

    public function find($item)
    {
        if (is_numeric($item)) {
            $this->data = $this->db->get('users', ['id', '=', $item])->first();
        } else {
            $this->data = $this->db->get('users', ['email', '=', $item])->first();
        }
        if ($this->data) {
            return true;
        }

        return false;
    }

    public function data()
    {
        return $this->data;
    }

    public function exists()
    {
        return $this->data();
    }

    public function isLogged()
    {
        return $this->isLogged;
    }

    public function logout()
    {
        $this->db->delete('user_cookies', ['user_id', '=', $this->data()->id]);
        Session::delete($this->sessionName);
        Cookie::delete(($this->cookieName));
    }

    public function update($fields, $id = null)
    {
        if (!$id && $this->isLogged()) {
            $id = $this->data()->id;
        }

        $this->db->update('users', $id, $fields);
    }

    public function hasPermissions($key = null)
    {
        $group = $this->db->get("`groups`", ['id', '=', $this->data()->group_id]);

        if ($group->count()) {
            $permissions = $group->first()->permissions;
            $permissions = json_decode($permissions, true);
            if ($permissions[$key]) {
                return true;
            } else {
                return false;
            }

        }
    }
}
