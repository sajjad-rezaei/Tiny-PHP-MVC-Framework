## Tiny-PHP-MVC  Framework
This is a lightweight PHP framework follows the MVC pattern.

It comes with default needs to make things simple and fast.

### This framework use following repository :

- [Medoo](https://github.com/catfan/Medoo)

    use as PHP database framework

- [JWT](https://github.com/firebase/php-jwt)

    A simple library to encode and decode JSON Web Tokens
- [Validation](https://github.com/davidecesarano/Validation)

   Simple PHP class for Validation

### The data structure is as follow:

Folder Name | Structure and Rules
------------ | -------------
public | The index.php is located here and all request will pass throw index
public > assets | all assets(images/js/css.etc..) will be located here
app | It contains all codes that build the framework, also bootstrap.php is located here that include all files
app > classes | All classes should be located here(classes start with upper case letter)
app > classes > core | The core class of framework is located here, if you want to add ability to this framework put your file here
app > configs | All constants and configs locate here
app > controllers | Controllers are here(camel case name with "Controller" suffix e.g "SamoleController.php")
app > helpers | Helper functions are here, we have function here not a class
app > hooks | All hook functions will locate here
app > middlewares | middlewares classes are here(classes start with upper case letter)
app > models | Models classes goes here
app > routes | Routes file are here(you can have as many files as you like or put all Routes in single file)
app > views | Html views goes here(you can have your own structure)

### Usage and conventions:
***

- ####Routes:
  
   Routes file are located in `routes` folder and you can have as many files as you like or put all Routes in single file
  
   router support `get/post/put/delete` http vervbs and can define Route as below:
  
   ```
    /*
    * $router is a global variable
    * pattern : request url witch is string
    * callback : call back witch should be excuted when the url mach with pattern, It can be callback function or string
    * callback : middlewares that should excuted or not executed witch is array
    */
    $router->get(pattern, callback ,middlewares);
   ```
  note that you can override the `USE_MIDDLEWARES_FOR_ROUTES` constant in `config.php` for middlewares with use `+/-` in middlewares part like below:
  
  ```
  //use - or + for auth(as we define in 'USE_MIDDLEWARES_FOR_ROUTES' constant the auth middlewate will be execute on all routes)
  $router->get('/home/:id/:name', 'samples@index' , ["-auth"]);
  //also you can use other middleware as well
  $router->get('/home/:id/:name', 'samples@index' , ["-auth","+someOtherMiddleWare"]);
  ```
  Lets see other examples of routes:
  
  ```
  // If you use SPACE in the url, it should convert the space to -, /home-index
  $router->get('/home index', 'samples@index');
  /controller pattern will be like "controller@action"
  $router->post('/home', 'samples@post' );
  //you can also give a path or subfolder that controller is located in
  $router->get('/home/subfolder', ['samples@index' , "subfolder"]); // e.g. "controllers/subfolder/SamplesController.php"
  
  //you can also use a naive call back function
  $router->get('/', function() {
  echo 'Welcome ';
  });
  ```
- #### Controllers:
   
  controllers should be camel case name with "Controller" suffix e.g "SamoleController.php"
  you can have as much as method you want in controller
  Validating data is important and you can validate data as follow:
  
  ```
     public function index(){
        /*
        * the validate function get the name and value witch name is the name witch will be shown in errors
        * getvalue(true) will sanitize and escape special char of given value
        */
        $data = [
            "name" => $this->validate("name" ,post('name'))->pattern("words")->required()->getValue(),
            "lastName" => $this->validate("lastName" , post('last_name'))->pattern("words")->getValue(),
            "age" => 20 
        ];
        //check if there is error
        $this->checkValidate();
        //load view(dashboard.php wich is located in "views/users" folder)
        $this->view("users/dashboard");

    }
  ```
  all the pattern can be found in `classes/core/Validation.php` file as below:
  ```
  public $patterns = array(
            'uri'           => '[A-Za-z0-9-\/_?&=]+',
            'url'           => '[A-Za-z0-9-:.\/_?&=#]+',
            'alpha'         => '[\p{L}]+',
            'words'         => '[\p{L}\s]+',
            'alphanum'      => '[\p{L}0-9]+',
            'int'           => '[0-9]+',
            'float'         => '[0-9\.,]+',
            'tel'           => '[0-9+\s()-]+',
            'text'          => '[\p{L}0-9\s-.,;:!"%&()?+\'°#\/@]+',
            'file'          => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
            'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
            'address'       => '[\p{L}0-9\s.,()°-]+',
            'date_dmy'      => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
            'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
            'email'         => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+[.]+[a-z-A-Z]'
        );
  ```
  you can edit or add new pattern
  get json data from request:
  ```
  global $request;
  #somekey can be empty that returns all data
  $jsonData = $request->input($someKey);
  ```

- #### models:
  
  model name should be uppercase and better to be singelar 
  we use Medoo framework for connecting to DB you can see have custom function in model file and use Medoo functionality to interact with DB for more detail see [Medoo Doc](https://medoo.in/doc)
  
  For simplicity we simulate CRUD for each model that can be found in `classes/core/model.php`, you should add all table column as public properties of model like below: 

  ```
  class Sample extends Model{

    public $tableName = "sample";
    public $PKName = "id";
    public $id;
    public $name;
    public $lastName;
    public $age;
    
    #use medoo for getting 
    public function test(){
        //this is a Medoo select function
        $result = $this->db->select();
    }

  }
  ```
  this properties use for simple CURD functionality and as you see you can have as many as methods as you like

  so how to use a model in controller is like below:
  
  first create intiante the model `$this->sampleModel = $this->model('sample');` we mostly do it in the constructor
  
  then:
  ```
  //data assosative array , kyes should be same with properties name
  $data = ["age" => 10 , "name" => "someName"] // let the last name to be default
  //map the data for propertires to be full always we need maping
  $this->sampleModel->map($data);
  
  //add to DB is as simple as you see
  $this->sampleModel->add();
  
  //condition structure and sample
  $condition = [
    ["id" , ">" , 1] , ["name" , "=" , "someName"]
  ];
  
  //OR update(we need condition if condition was empty then the PKName will be its value.e.g. "id = $id")
   $this->sampleModel->update($condition);
  
  //OR delete(we need condition if condition was empty then the PKName will be its value.e.g. "id = $id")
   $this->sampleModel->delete($condition);
  ```
  
  For `select` do as below:

  ```
  /*
  * columns can be array(['id' , 'name']) OR just simply "*"
  * condition is same as above condition array
  * type is what you add globally in config.php as MEDOO_SELECT_TYPE or can use custom MEDOO_OBJ or MEDOO_ARRAY  constants
  */
  $this->sampleModel->getRow($columns , $condition , $type)
  
  $this->sampleModel->get($columns , $condition , $type)
  ```
  
- #### hooks:
  Hooks are located in `hooks` folder that you can put as many files as you want or pull all hooks in the one php file.

  Concept of hooks woks with `add_hook` and `get_hook` functions, you should put the `add_hook` function in the hook folder and the `get_hook` function where ever you want to have the hook
  the structure of these functions are as below:
  ```
  get_hook(hookName, ...args )
  
  add_hook(hookName, callBackFunc)
  ```
  e.g.
  ```
  add_hook("logger_hook" , function($message , $messageType){
    // do some thing
  });
   //OR
  function hookWithName($message , $messageType){
    //do some thing
  }
  add_hook("logger_hook" ,"hookWithName");
  ```
  And then get the hook:

  ```
  //if the hook added it will be executed 
  get_hook("logger_hook" , $message , $messageType);
  ```
  By default, we have a hook in `logger.php` class it name is `logger_hook` as you see above, It sends message and messageType to the hook.
  
- ####views:
  
  views are located in "views" folder and you can have your own structure from there on

  you can render a view by using it in the controlle like so :

  ```
  /*
  * the first param is address of file, the address begin from view folder to the file
  * the second param is the data that will be send to the view
  * the third param says whether this data can show as json or not(default = false) 
  */
  $this->view("templates/home/index" , ["message" => "hi all , Welcome to this tiny framework"] , false);
  ```
  Note: you can access data with `$data` variable in your view php file
  
  Note: you can make data to print as json if you set `RETUN_TYPE=json` in request header and also send true in thirdth param of `$this->view` function.

NOTES:
   
If you set a `RETURN_TYPE` to json all data will be print as json even if yo use `$this->view()` in controller 
  
  
