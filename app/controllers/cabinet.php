<?php

namespace App\Controllers;

use App\Controllers\Controller;

class Cabinet extends Controller
{
    public function getCabinetPage()
    {
       $this->secure(); 
       return $this->getPage('cabinet.html', []); 
    }
}
