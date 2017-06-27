<?php
//set data
$data={{data}};


//include the core
$config = $data["config"];
include($config["filesystem.framework"] . "/app.php");

//include the core of the framework
$app = new App();
$app->setConfig($config);
$app->includeCore();

//create a class to execute the function
class executionCode extends \hodphp\core\Base
{
        function execute(){

                //Run the code in the right context
                $paramData=$this->prepare();

                //simulate the parameters given in the function
                foreach($paramData["paramNames"] as $paramKey=>$paramName){
                        ${$paramName}=$paramData["data"][$paramKey];
                }

                //run the code
                {{code}}

                //and remove this file
                $this->finish();
        }

        function prepare(){
                global $data;
                $this->globals->initialize($data["globals"]);
                $this->db->parent=$data["dbParent"];
                $this->session->simulateFakeSession($data["session"]);
                return ["data"=>$data["data"],"paramNames"=>$data["paramNames"]];
        }

        function finish(){
                global $data;
                $this->filesystem->rm($data["fileName"]);
        }
}


//and run the code
$instance=new executionCode();
$instance->execute();
