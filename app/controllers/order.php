<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Order as OrderModel;

Class Order extends Controller
{
    public function makeOrder($cart) 
    {
        if (isset($_SESSION['cart'])) {
            $order = new OrderModel;
            $order->date = date("Y-m-d H:i:s");
            $order->name = 'test name';
            $order->phone ='+79176450029';
            $order->comments = 'нужно доставить';
            $order->processflag = 'new';
            $order->save();
            $order->addStrings($cart);

            $_SESSION['cart'] = null;
        }

        return "СПАСИБО";
    }

    public function showAllOrders() 
    {
        $this->secure();
        $order = new OrderModel;
        $orders = $order->getOrderSortedByDate();
        
        $out = $this->getPage('orders.html', [
            'orders' => $orders,
        ]); 

        return $out;
    }

    public function changeOrderStatus(int $id)
    {
        $this->secure();
        $order = new OrderModel;
        $order->getOneById($id);

        switch ($order->processflag){
            case 'new':
                $order->processflag = 'done';
                break;
            case 'done':
                $order->processflag = 'new';
                break;
        }

        $order->save();

        $this->redirect('/orders');
    }

}



