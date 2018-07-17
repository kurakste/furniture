<?php

namespace App\Controllers;

use App\Controllers\Controller;

class Cart extends Controller 
{

    public function addItemToCart() {
        $cartstr = [
            'iid' => $_POST['item'],
            'image'=>$_POST['imagefilename'],
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'amount' => $_POST['amount']
        ];

        $needToAdd = true; // Если да то нужно добавить к новую строчку.

        /* 1. Есть ли в массиве уже такой  ID? */ 
        /* 2. Если есть нужно сложить то кол-во которое есть */ 
        /* в корзине с тем, что необходимо добавить. если нет - */
        /* нужно просто добавить новую строку. */ 

        if ($_SESSION['cart']) {
            foreach ($_SESSION['cart'] as &$carts) {
                if ($carts['iid'] == $cartstr['iid']) {
                        $carts['amount'] += $cartstr['amount'];
                        $needToAdd = false;
                } 
            }
            if ($needToAdd) {
                $_SESSION['cart'][] = $cartstr;
            }
        } else {
        // Если корзины еще не создано то просто добавим строку.
                $_SESSION['cart'][]=$cartstr;
            }

        header('Location:/catalog');
        die;
        return "Add item"; 
    }

    public function clearCart()
    {
        $_SESSION['cart'] = null;
        header('Location: /catalog');
        die;
    }

    public function getCartsItem()
    {
        $cart = $_SESSION['cart'] ?? null;
        return $cart;    
    }

    public function showCart() 
    {
        $cart = $_SESSION['cart'] ?? null;

        $out = $this->getPage('cart.html', [
            'cart' => $cart,
        ]); 

        return $out;
    
    }

}
