<?php
/**
 * Класс - каркас приложения.
 * Задача класса - обеспечить инициализацию приложения, роутинг и жизнено 
 * важный функцианал. Предоставляет адаптеры для внешних библиотек. 
 * Встроен следующий функционал:
 *  - чтение параметров приложения из файла и доступ к ним через статический 
 *      метод класса ::getParams().
 *  - инициализация базы данных. Возврат идентификатора вызов метода::getDb()
 *  - роутинг - обеспечивает простой роутинг. Роутинг нужно явно запустить 
 *  через метод ::doRouting()
 *  - организация CSRF защиты форм методами ::getCsrf() для получения кода 
 *  защиты формы и метода ::checkCsrf($inputs). 
 *  - организация редиректов и ответов сервера (формирование корректных 
 *      html заголовков). ??? возможно надо перенести в класс контроллеров.
 *  - встроеная проверка reCapture. Вызывается методом ::chekReCapture($input)
 *  - встроеная возможность логирования ошибок. ::writeLog($type, $message);
 *  - встороеная возможность отправлять сообщения на почту администраторам
 *   ::toAdmins($subject, $message);
 *   - встроеная проверка ввода????? нужно подумать. Может это функция 
 *   контроллера ввод-вывод
 *
 *
 */

namespace App;

use App\Router;

class App {


      private static $pdo;
      private static $settings;
      private static $_instance; 

    /**
     * Конструктор производит инециализацию всех необходимых сервисов которые
     * потом будут использовать части приложения. Например, нужно создать 
     * экземпляр класса логера и присвоить ссылку на него переменной 
     * self::$loger . Функция ::writeLog() - это обертка для логера, котора 
     * обеспечит более удобный доступ к методам класса логера и даст возможность
     * делать настройку экземпляра класса если это необходимо.
     */
    private function __construct() {    
        self::initSettings();
        $this->dbOpen();

    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }
  
    private function __clone() {
    }

    private function __wakeup() {
    }     


    private static function initSettings() 
    {
        $settingsFile = __DIR__."/../settings.php";
        if (is_readable ($settingsFile)) {
            self::$settings = require_once ($settingsFile);
        
        } else {
            throw new \Exception('Не найден файл настроек /../settings.php');
        } 
    }

    public static function doRouting() 
    {
        $router = new Router;
        $out = $router->callController();
        return $out;

    } 

    public function dbOpen()
    {
        $params = self::$settings['db'];

        $charset = 'utf8';
        $dns = "mysql:host={$params['host']};dbname={$params['dbname']};charset ={$charset}";
        $opt = [
            \PDO::ATTR_ERRMODE               =>  \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE    =>  \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES      =>  false,
        ];

        self::$pdo = new \PDO ($dns, $params['user'], $params['password'], $opt);
        self::$pdo->exec("set names utf8");
    }

    /**
     * Function closes DB conection;
     *
     * @return void
     * @author kurakste
     */
    public function dbClose() 
    {
        /* mysqli_close(self::$db); */
    }
    
    /**
     * Вернет идентификатор базы данных.
     *
     */

    public static function getDb() 
    {
        return self::$pdo; 
    }


    public static function getSettings($param)
    {
        return self::$settings[$param];
    }
}

