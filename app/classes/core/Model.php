<?php

class Model {

    /**
     * @var
     */
    public $db;

    /**
     *  Construct
     */
    public function __construct() {
        //just load the database
        $this->db = new Medoo([
            'database_type' => DATABASE['Type'],
            'database_name' => DATABASE['Name'],
            'server' => DATABASE['Host'],
            'username' => DATABASE['User'],
            'password' => DATABASE['Pass'] ,
            'error' => PDO::ERRMODE_EXCEPTION
        ]);
        
        
        
    }
    public function map($properties){

        foreach($properties AS $property => $value ){
            
            $this->$property = $value;
            
        }

    }
    private function clearArray($data , $omittedProps = []){
        global $logger;
        if(!isset($data['tableName']))
            $logger->log("variable \$tableName is not defined in the model!!" , $logger::FATAL);
        
            

        array_push($omittedProps , 'tableName');
        array_push($omittedProps , 'db');
        array_push($omittedProps , 'PKName');
         // remove all empty  value of indexes  query
        $data = array_filter($data , function ( $value , $key) use ($omittedProps){
            if(
                !in_array($key , $omittedProps) AND
                (
                    $value === 0 OR
                    $value === false OR
                    !empty($value) 
                )
            ) return true;

        } , ARRAY_FILTER_USE_BOTH  );
        

        
        return $data;

    }
    public function createCondition($condition , $isSelect = false){
        global $logger;
        $finalCon = [];
        if(!empty($condition)){
            
            foreach($condition AS $con){
                if(count($con) != 3)
                    $logger->log("Condition Parameters Are Not Equal" , $logger::FATAL);
                //ommit the =
                if($con[1] === "=")
                    $con[1] = "";
                else
                    $con[1] = "[" . $con[1] . "]";

                $finalCon[$con[0].$con[1]] = $con[2] ;
            }

        }
        else
            if(!$isSelect)
                $finalCon[$this->PKName] = $this->{$this->PKName};
        
        return $finalCon;

    }
    public function invokeQueries($func ,  ...$args){

        global $logger;
        $result = false;
        
        try{
            $result = call_user_func_array($func , $args);
        }catch (PDOException $e) {
            
                $logger->log("MYSQL =>  " . $e->getMessage() .
                 PHP_EOL . json_encode($this->db->log()) , $logger::FATAL);
            
        }
        
        return $result;

    }
    public function add(){
        
        $properties = get_object_vars($this);
        $properties = $this->clearArray($properties );
        $result = $this->invokeQueries([$this->db , "insert" ] , $this->tableName , $properties);
        return $result;

    }
    //update([['id', '>' , 2] , ['age', '!', 3]] , ['id'])
    public function update($condition = [] , $omittedProps = []){

        $properties = get_object_vars($this);
        
        $condition = $this->createCondition($condition);
        //lets ommit the Pk
        if(empty($omittedProps) OR !isset($omittedProps[$this->PKName]))
            array_push($omittedProps , $this->PKName);

        $properties = $this->clearArray($properties , $omittedProps);
        
        $result = $this->invokeQueries([$this->db , "update" ] , $this->tableName , $properties , $condition);
        return $result;

    }
    public function delete($condition = []){
          
        $condition = $this->createCondition($condition);
        $result = $this->invokeQueries([$this->db , "delete" ] , $this->tableName  , $condition);
        return $result;

    }
    public function get($columns = "*" , $condition = [] , $type = false ){
          
        $condition = $this->createCondition($condition , true);
        $result = $this->invokeQueries([$this->db , "select" ] , $this->tableName  , $columns , $condition);
        return $this->returnSelectType($result , $type);

    }
    public function getRow($columns = "*" , $condition = [] , $type = false ){
          
        $condition = $this->createCondition($condition , true);
        $result = $this->invokeQueries([$this->db , "get" ] , $this->tableName  , $columns , $condition);
        return $this->returnSelectType($result , $type);

    }   
    private function returnSelectType($data , $type){

        switch($type){
            case false:
                return returnSelectType($data , MEDOO_SELECT_TYPE);
            case MEDOO_OBJ:
                return returnSelectType($data , MEDOO_OBJ);
            case MEDOO_ARRAY:
                return returnSelectType($data , MEDOO_ARRAY);
            default:
                return $data;
        }
        

    }
    public function count($columns = "*" , $condition = [] , $type = false ){
          
        $condition = $this->createCondition($condition , true);
        $result = $this->invokeQueries([$this->db , "count" ] , $this->tableName  , $columns , $condition);
        return $this->returnSelectType($result , $type);

    }
    public function id(){
        return $this->db->id();
    }


}