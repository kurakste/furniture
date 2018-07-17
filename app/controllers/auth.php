<?php

namespace App\Controllers;

use App\Models\User;

Class Auth extends Controller 
{
    const SOLT = 'hdasfkjsdhfkjsafd';

    /* 
     * Выводит форму регистрации нового пользователя. 
     */
    public function registerForm()
    {
        return $this->getPage('register.html', []); 
    }

    /*
     * Проверяет данные из формы регистрации нового пользователя и если все ок,
     * вносит данные пользователя в базу данных. Если были ошибки, возвращает на
     * страницу регистрации пользователя и передает массив ошибок 
     * err[]['emessage']
     */

    public function saveUser()
    {
        $err = $this->chekRequiredFields(
            ['name', 'email', 'password', 'password1']
        );

        if (count($err)>0) return $this->getPage('register.html', ['err' => $err]); 

        if ($_POST['password']!==$_POST['password1']) {
            $err =[];
            $err[]['emessage']='Пароли не совпадают.';
        }

        if (count($err)>0) return $this->getPage('register.html', ['err'=> $err]); 

        extract($_POST);

        $usr = new User;
        if (!($usr->isEmailFree($email))) {
            $err =[];
            $err[]['emessage']='Пользователь с таким именем и паролем существует.';
            return $this->getPage('register.html', ['err'=> $err]);
        };

        $usr->name  = $name;
        $usr->email = $email;
        $usr->phash = crypt($password, Auth::SOLT);

        $usr->sessionId =  $usr->generateSessionId();
        $usr->save();
        $this->setUserAsAuth($usr); 
    }

    /*
     * После того, как пользователь введет логин(email) и пароль,
     * данные нужно передать функции chekUser(). 
     *
     */
    public function loginUser() 
    {
        return $this->getPage('login.html', []);
    }

    /*
     * Провераяет есть ли пользователь с таким логином и паролем.
     * Если есть то авторизует его в системе и устанавливает все 
     * необходимые данные в базе данных и в сесии. 
     */
    public function chekUser() {

        extract($_POST);
        $user = new User;
        $tmp=$user->getUserByEmail((string)$email);
        if (!$tmp) {
            $err = [];
            $err[]['emessage'] = 'Пользователь с таким логином и паролем не существует.';
            $this->redirect('/login');
        }

        $phash = crypt($password, Auth::SOLT);
        $res = $user->chekUserPassword($phash);
        if ($res) {
            $this->setUserAsAuth($user);
            $this->redirect('/cabinet');
        } else {
            $err = [];
            $err[]['emessage'] = 'Пользователь с таким логином и паролем не существует.';
            return $this->getPage('login.html', ['err'=>$err]);
        }
}


    private function setUserAsAuth(User $user)
    {
        setcookie("userId", $user->getId(), time()+ 2592); 
        setcookie("sessionId", $user->sessionId, time()+ 2592); 
    }



    public function logout()
    {
        setcookie('userId', '', time()-1);
        setcookie('sessionId', '', time()-1); 
        $this->redirect('/');
    }

    public function cabinet()
    {
        $this->secure(); // проверка аутентификации. 
        $this->saveHistoryForUser(); //пищем историю посещений
        $user = new User;
        $user->getOneById((int)$_COOKIE['userId']);
        
        return $this->getPage('cabinet.html', [
            'user' => $user,
        ]); 
    }

    public function protected()
    {
       $this->secure(); // проверка аутентификации. 
       $this->saveHistoryForUser();//пишем историю посещений.
       return "<h1> SECURE PAGE </h1>";
    }
}
