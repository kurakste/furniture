<?php 

namespace App;

use App\Models\Item;
use App\Controllers\Catalog;
use App\Controllers\Cart;
use App\Controllers\Test;
use App\Controllers\Order;
use App\Controllers\Auth;
use App\Controllers\Cabinet;

Class Router 
{
    protected $params; // Массив состоящий из элементов пути
    protected $method; // Метод которым обратились к приложению.
                       // www.site.ru/param0/param1/param2/...
                       // 0 элемент -  param0, и т.д. 
    /**
     *  $this->method -  название метода, который был использован
     *  $this->params - параметры. То что было передно в адресной строке
     *  www.site.ru/param0/param1/param2/...
     *
     *   @return Функция может вернуть результат работы контроллера. 
     *   Например срендеренный вид. По текущий задумке это будет то 
     *   единственное, что скрипт выведет в браузер оператором echo.  
     *   Еще один возможный вариант - роутер или контролер могут отправить
     *   HTTP ответ (например сообщение об ошибке при обращении к не 
     *   существующей ошибке или редирект. В этом случае управление 
     *   не возвращается в приложение. ЭТО НУЖНО ОБДУМАТЬ)
     */

    public function callController()  
    {
        $this->getMethodAndPath();
        $out = '';
        switch ($this->params[0]) {

        case '':
            $cat = $this->params[1] ?? 0;
            $cat = (int)$cat;
            if ($cat <= 0) $cat = 0;
            
            $out = (new Catalog)->getCatalog($cat);
            break;

        case 'catalog':
            $cat = $this->params[1] ?? 0;
            $cat = (int)$cat;
            if ($cat <= 0) $cat = 0;
            
            $out = (new Catalog)->getCatalog($cat);
            break;

        case 'clearcart':
            $out = (new Cart)->clearCart();
            break;

        case 'cart':
            $out = (new Cart)->showCart();
            break;
        
        case 'orders':
            $out = (new Order)->showAllOrders();
            break;

        case 'makeorder':
            $cart = $_SESSION['cart'] ?? null;
            $out = (new Order)->makeOrder($cart);
            break;

        case 'change-order-status':
            $id = $this->params[1] ?? 0;
            $id = (int)$id;
            $out = (new Order)->changeOrderStatus($id);
            break;

        case 'item':
            $itemId = $this->params[1] ?? 1;
            $itemId = (int)$itemId;
            if ($itemId <= 0) $itemId =1;
            
            $out = (new Catalog)->getItem($itemId);
            break;

        case 'addtocart':
            $out = (new Cart)->addItemToCart();
            break;

        case 'login':
            $out = (new Auth)->loginUser();
            break;
        
        case 'register':
            $out = (new Auth)->registerForm();
            break;
        
        case 'logout':
            $out = (new Auth)->logout();
            break;

        case 'save-user':
            $out = (new Auth)->saveUser();
            break;
        
        case 'do-login':
            $out = (new Auth)->chekUser();
            break;

        case 'cabinet':
            $out = (new Cabinet)->getCabinetPage();
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            die;
        }
        return $out;
    }

    // Вспомогательная функция. Извлекает метод и разбирает урл.
    private function getMethodAndPath()
    {
        // Извлекаем название метода.
        $this->method = $_SERVER['REQUEST_METHOD'];
        // извлекаем содержание адресной строки и упаковаваем массив
        $arr = explode('/', $_SERVER['REQUEST_URI']);
        $arr = array_filter($arr); 

        //Сейчас в массиве $arr сейчас компоненты адресной строки
        //если запрос типа GET то в конце строки могут оказаься параметры
        //вида  ?param1=fjsalf&param2=sdsadas
        //Нужно проверить последний элемент и исключить из него параметры:

        $tmp = array_pop($arr);
        $tmp = explode('?', $tmp);
        $tmp = $tmp[0];
        $arr[] = $tmp;
        $this->params = array_values($arr);
    }

}
