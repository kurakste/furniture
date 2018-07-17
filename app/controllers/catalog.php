<?php

namespace App\Controllers;

use App\Controllers\Contorller;
use App\Models\Category;
use App\Models\Item;

class Catalog extends Controller
{
    public function getCatalog(int $cid)
    {
        $cat = new Category;
        $cats = $cat->getAll();
        $item = new Item;
        $items = $item->getAllItemFromCatgory($cid);

        $cart = $_SESSION['cart'] ?? null;
        $cartscount = $cart ? count($cart) : null;


        $out = $this->getPage('index.html', [
            'cartscount' => $cartscount,
            'items' => $items,
            'cart' => $cart,
        ]); 

        return $out;
    } 

    public function getItem($id)
    {
        $item = new Item;
        $item->getOneById($id);

        $cart = $_SESSION['cart'] ?? null;

        $out = $this->getPage('item.html', [
            'item' => $item,
            'cart' => $cart,
        ]); 

        return $out;
    }

}
