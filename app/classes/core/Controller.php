<?php
  /* 
   *  CORE CONTROLLER CLASS
   *  Loads Models & Views
   */
  class Controller {

    private $validator;
    // Lets us load model from controllers
    public function model($model){
      // Require model file
      require_once MODELS . ucfirst($model) . '.php';
      // Instantiate model
      return new $model();
    }

    // Lets us load view from controllers
    public function view($url, $data = [] , $allowJson = false){
      global $logger;
     //check the return type
       //return json if requested the json

      if($allowJson and getHeader('RETURN_TYPE') and strtolower(getHeader('RETURN_TYPE')) == 'json'){
          jsonResponse($data);
      }

      // Check for view file
      if(file_exists(VIEWS.$url.'.php')){
        // Require view file
        require_once VIEWS.$url.'.php';
      } else {
        // No view exists
        $logger->log('View does not exist');
        $logger->printMessage();

      }
    }
    public function validate($name , $value){

      if(!(!empty($this->validator) AND $this->validator instanceof Validation))
        $this->validator = new Validation();
      return $this->validator->name($name)->value($value);


    }
    public function checkValidate(){
      global $logger;

      if($this->validator->isSuccess()){
        return true;
      }else{
        $logger->log('Validation error! ' . json_encode($this->validator->getErrors()));
        $logger->printMessage();

      }


    }
    
    

  }