<?php

namespace App\Models;

use App\App;

class User extends Model
{
    public $name;
    public $email;
    public $phash;
    public $phone;
    public $sessionId; // Будет храниться в куках, если пользователь авторизирован.
    public $role;
    
    public function isEmailFree(string $email) 
    {
        $tableName = $this->getTableName();
        $sql ="SELECT count(email) as count FROM {$tableName}s WHERE email = :email";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch(\PDO::FETCH_LAZY);

        $out = ($row->count == 0) ? true : false;
        return $out;
                
    }

    public function generateSessionId() 
    {
        return md5((date("Y-m-d H:i:s")));
    }

    public function chekUserPassword($phash) 
    {
        $out = ($this->phash == $phash) ? true : false;
        return $out; 
    }

    public function getUserByEmail($email)
    {
        $tableName = $this->getTableName();
        $sql ="SELECT * FROM {$tableName}s WHERE email = :email";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch(\PDO::FETCH_LAZY);
        if ($row) {
            foreach ($row as $key=>$value) {
                if ($key == 'queryString') continue;
                $this->{$key} = $value;
            }
            return true;
            } else {
                return false;
        }
    }

    
}
