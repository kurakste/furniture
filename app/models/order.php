<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Ostring;
use App\App;

class Order extends Model
{
    public $date;
    public $name;
    public $phone;
    public $comments;
    public $processflag;

    public function getStrings()
    {
        $sql ="SELECT * FROM ostrings WHERE oid= :oid;";

        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'oid' => $this->getId()
        ]);
        
        return $stmt->fetchAll(); 
    }

    public function addStrings(array $strings) {
        foreach ($strings as $string) {
            $ostr = new Ostring;
            $ostr->oid = $this->getId();
            $ostr->name = $string['name'];
            $ostr->price = $string['price'];
            $ostr->amount = $string['amount'];
            $ostr->save();
        }
    }

    public function getOrderSortedByDate() 
    {
        $tableName = $this->getTableName();
        $sql ="SELECT * FROM {$tableName}s ORDER BY date DESC;";
        $pdo = App::getDb();

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $out = $stmt->fetchAll();
        return $out;
    }
}
