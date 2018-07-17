<?php

namespace App\Models;

use App\Models\Model;
use App\App;

Class Item extends Model 
{
    public $name;
    public $description;
    public $cid;
    public $size;
    public $mainimageid;
    public $price;

    /*
     * Возвращает массив объектов Item
     * соответствующих конкретной категории.
     */
    public function getAllItemFromCatgory($cid)
    {
        $tableName = $this->getTableName();
        if ($cid == 0) {
            $sql ="SELECT * FROM {$tableName}s ;";
            $data =[];
        } else {
            $sql ="SELECT * FROM {$tableName}s WHERE cid = :cid";
            $data = ['cid'=>$cid];
        }

        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
        $class = get_called_class();
        $out =[];

        foreach ($stmt as $i) {
            $item = (new $class);
            $item->getOneById($i['id']);    
            $out[] = $item;        
        }
        return $out;
    }
    /*
     * Возвращает массив имен изображений этого
     * товара.
     */

    public function getImages() 
    {
        $tableName = $this->getTableName();
        $iid = $this->id;
        $sql ="SELECT * FROM images WHERE iid = :iid";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['iid'=>$iid]);
        return $stmt->fetchAll(); 
    }

    public function getMainImage()
    {
        $iid = $this->mainimageid;
        $sql ="SELECT filename FROM images WHERE id = :iid";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['iid'=>$iid]);
        $out = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $out['filename'];
    }

}

