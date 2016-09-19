<?php
namespace lib\template\functions;

use core\Loader;

class FuncRenderAction extends \lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(),$module=false)
    {
        return $this->response->fakeResponse(
            $parameters,
            function ($parameters) {
                if(is_array($parameters[0])){
                    $parameters=$parameters[0];
                }
                $oldPartialMode=$this->response->partialMode;
                $this->response->setPartialMode(true);
                Loader::loadAction($parameters);
                $this->response->setPartialMode($oldPartialMode); //and to not be destructive..
            }
        );
    }
}

?>