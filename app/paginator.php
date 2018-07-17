<?php

namespace App;

/*
 *  Клас преднозначен для работы с объектами калссов наследников класса
 *  Model. 
 *
 *  По-хорошему нужено определить интрефейс ModelsInterface и 
 *  ограничивать переданный объект этим интерфейсом. 
 *
 *  На вход подаем экземпляр класса. Кол-во страниц. На выходе 
 *  он отдает объект который может:
 *      - извлекать в массив объекты постранично
 *      - говорить сколько страниц всего есть в этом объекте
 *      - какая страница извлечена сейчас
 */

Class Paginator
{
    protected $count;  //  Сколько элементов на странице. 
    protected $countOfPage;
    protected $pageNumber=0;
    protected $page;
    protected $model;

    public function __construct ($model, $countOfItemOnPage) 
    {
        $this->count = $countOfItemOnPage; 
        $this->model = $model;
        $this->countOfPage = ceil($model->getAmount()/$countOfItemOnPage);
        $start = $this->pageNumber * $this->count;
        $this->page = $model->getAllAsArray($start, $this->count);
    }

    public function getPage($page)
    {
        $this->pageNumber = $page;
        $page = (int)$page - 1; // Пусть страницы нумеруются с 1-ой а не нуловй.
        if (($page<0) || ($page>$this->countOfPage)) throw new \Exception('Введено не корректное занчение страницы');
        $start= $page * $this->count;
        $this->page = $this->model->getAllAsArray($start, $this->count);
        return $this->page;
    }

    public function nextPage(){
        $this->pageNumber++;
        $this->page = $this->getPage($this->pageNumber);
        return $this->page;
    }
    public function prevPage(){
        $this->pageNumber--;
        $this->page = $this->getPage($this->pageNumber);
        return $this->page;
    }

    public function getPageCount()
    {
        return $this->countOfPage;
    }



}
