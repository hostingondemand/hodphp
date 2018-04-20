<?php
namespace hodphp\provider\templateFunction;

use hodphp\core\Loader;

class RenderAction extends \hodphp\lib\template\AbstractFunction
{

    //make a text lowercase
    function call($parameters, $data, $content = "", $unparsed = Array(), $module = false)
    {
        return $this->response->fakeResponse(
            $parameters,
            function ($parameters) {

                if (isset($parameters[0]) && is_object($parameters[0])) {
                    $p0 = $parameters[0]->getData();
                }

                if (isset($p0) && is_array($p0)) {
                    $parameters = $p0;
                }

                $oldPartialMode = $this->response->partialMode;
                $this->response->setPartialMode(true);
                Loader::loadAction($parameters);
                $this->response->setPartialMode($oldPartialMode); //and to not be destructive..
            }
        );
    }
}

