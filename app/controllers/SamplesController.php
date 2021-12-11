<?php

class Samples extends Controller
{
    
    public function __construct(){
        // Load Models
        //$this->[name]Model = $this->model('');
        $this->sampleModel = $this->model('sample');
    
    }
    public function index(){
        


        $this->view("templates/home/index" , ["message" => "hi all , Welcome to this tiny framework"]);

    }


    



}