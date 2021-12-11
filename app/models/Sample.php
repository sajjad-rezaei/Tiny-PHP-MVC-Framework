<?php

class Sample extends Model{

    public $tableName = "sample";
    public $PKName = "id";
    public $id;
    public $name;
    public $lastName;
    public $age;
    
    #use medoo for getting 
    public function test(){
        $result = $this->db->select();
    }

}

