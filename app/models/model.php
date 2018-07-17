<?php 

namespace App\Models;

use App\App;

Class Model 
{
    protected $id = null;
    /**
     * Заполнит поля объекта данными из базы данных.
     * Название таблице в BD будет образовано от названия класса
     * добавлением множественного числа.
     */ 

    public function getOneById($id) 
    {
        $tableName = $this->getTableName();
        $sql ="SELECT * FROM {$tableName}s WHERE id = :id";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id'=>$id]);
        $row = $stmt->fetch(\PDO::FETCH_LAZY);
        foreach ($row as $key=>$value) {
            $this->{$key} = $value;
        }
        return true;
    }

    /**
     * Вернет массив объектов из базы данных из таблицы с названием
     * класса во множественном числе. 
     */

    public function getAll() 
    {
        $tableName = $this->getTableName();
        $sql ="SELECT * FROM {$tableName}s;";
        $pdo = App::getDb();

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $class = get_called_class();
        $out =[];

        foreach ($stmt as $i) {
            $item = (new $class);
            $item->getOneById($i['id']);    
            $out[] = $item;        
        }

        return $out;
    }

    /**
     * Вернет кол-во объектов из базы данных из таблицы с названием
     * класса во множественном числе. 
     */
    public function getAmount()
    {
        $tableName = $this->getTableName();
        $sql ="SELECT COUNT(*) as count FROM {$tableName}s;";
        $pdo = App::getDb();

        $stmt = $pdo->query($sql);
        $out = $stmt->fetchAll();

        $out = $out[0]['count'];
        return $out;
    }

    /**
     * Возвращает все объекты этого класса. По умолчанию вернет 6-ть
     * объектов начиная с 0-го по 5-ый (не включтильно)
     *
     */
    public function getAllAsArray($offset=0, $count=6) 
    {

        $tableName = $this->getTableName();
        $sql ="SELECT * FROM {$tableName}s ORDER BY id ASC LIMIT {$offset}, {$count};";
        $pdo = App::getDb();

        $stmt = $pdo->query($sql);
        $out = $stmt->fetchAll();

        return $out;
    }

    /**
     * Создаст новую запись в таблице если объект новый или
     * обновит запись в базе данных если объект уже существовал 
     * ранее. 
     */
    public function save() 
    {
        $table = $this->getTableName().'s';
        $fields = get_object_vars($this); 
        $fields_str = '(';
        $values_str ='(';
        foreach ($fields as $key=>$value) {
            if ($key == 'id') continue;
            if ($key == 'queryString') continue;
            $fields_str .= $key.', '; 
            $values_str .= '\''.$value.'\', ';
        }
        $fields_str = substr($fields_str, 0, -2).')';
        $values_str = substr($values_str, 0, -2).')';

        $pdo = App::getDb();

        if ($this->id == null) {

            $sql = "INSERT INTO {$table} {$fields_str} VALUES {$values_str};";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $id = $pdo->lastInsertId();
            $this->id = $id;

        } else {
            $set_str = $this->getColValFromAssocForUpdate($fields);
            /* echo $set_str; die; */
            $sql = "UPDATE {$table} SET {$set_str}". 
                " WHERE id ='{$this->id}';";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }

    /**
     * Удалит запись с указанным  id из базы данных.
     *
     */
    public function delById($id)
    {
        $table = $this->getTableName().'s';
        $sql = "DELETE FROM {$table} WHERE id={$id};";
        $pdo = App::getDb();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    private function getColValFromAssocForUpdate(array $input)
    {
        $out ='';
        foreach ($input as $key=>$value) {
            if ($key == 'id') continue;
            if ($key == 'queryString') continue;
            $out .= $key.'=\''.$value.'\', ';
        }
        $out = substr($out, 0, -2);


        return $out;
        
    }

    public function getId()
    {
        return $this->id;
    } 

    protected function getTableName()
    {
        $className = get_called_class();
        $tmp = explode('\\', $className);
        return array_pop($tmp);
    }
}

