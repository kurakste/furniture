<?php 

namespace App\Controllers;

use App\Models\User;
use App\Models\Link;

class Controller
{
    /* Проверяет установлена ли у текущего пользователя валидное сочитание 
     * ID пользователя и ID сессии. Если нет, делает редирект на станицу 
     * входа пользователя. 
     * Если в функцию передано значение роли, то дополнительно будет проведена
     * проверка роли. Если роль не соответствует, то будет произведена переад
     * переадресация на страницу авторизации. 
     */

    public function secure($role = null)
    {
        $id = $_COOKIE['userId'] ?? null;
        if ($id) {
            $session = $_COOKIE['sessionId'] ?? '';
            $user = new User;
            $res = $user->getOneById($id);
            if (!$res) {
                header('Location: /login');
                die;
            }
        } else {
            header('Location: /login');
        }

        if (!($session == $user->sessionId)) {
            header('Location: /login');
            die;
        }
        //  Если проверка роли задана и роль текущего пользователя
        //  не совпадает с ролью переданной в аргументе переадресовать 
        //  на страницу авторизации. 
        if ($role ) {
            if ($role != $user->role) {
            header('Location: /login');
            }
        }

    }
    
    // Подставляет переменные и рендерит шаблон.
    public function getPage($template, $data)
    {

        $loader = new \Twig_Loader_Filesystem(
            __DIR__.'/../../view'
        );

        $twig = new \Twig_Environment(
            $loader, 
            array('cache' => false,)
        );
        $this->saveThisPageAsLast(); // Сохраняем ссылку для возврата.
        $out = $twig->render($template, $data);
        return $out;
    }

    // Проверяет пришли ли с пост запросом необходимые имена переменных. 
    // Если пришли, можно делать extract и пользоваться ими. 
    protected function chekRequiredFields(array $input)
    {   
        $out=[];
        foreach ($input as $field){
            if (!isset($_POST[$field]) || $_POST[$field] == '') {
                $out[]['emessage'] = "Заполните пожалуйста поле {$field}.";
            }

        }
        return $out;
    }

    // Просто хелпер редиректор + die
    public function redirect(string $path)
    {
        $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
        header("Location: $path");
        die;
    }

    // Хелпер возвращает на предыдущую страницу.
    // Сработает если предыдущий перехо был через redirect()
    //  или вызывался метод saveThisPageAsLast();
    public function goBack() 
    {
        $this->redirect($_SESSION['lastPage']);
    }

    public function saveThisPageAsLast()
    {
        $_SESSION['lastPage'] = $_SERVER['REQUEST_URI'];
    }
}
