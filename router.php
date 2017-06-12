<?php
    $url=$_SERVER["REQUEST_URI"];
    $routerStarted=true;
    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/.htaccess")){
        $contents = file($_SERVER["DOCUMENT_ROOT"]."/.htaccess");
        foreach($contents as $line) {
            if(strtolower(substr($line,0,11))=="rewriterule"){
                $rule=explode(" ",$line);
                $pattern="/".$rule[1]."/";
                $replacement=$rule[2];
                $url=preg_replace($pattern,$replacement,$url);
            }
        }
    }

    $exp=explode("?",$url);
    if(count($exp)>1){
        $expQuery=explode("&",$exp[1]);
        foreach($expQuery as $var){
            if($var) {
                $expVar = explode("=", $var);
                $_GET[$expVar[0]] = $expVar[1];
            }
        }
    }
    $file=$exp[0];


    include($_SERVER["DOCUMENT_ROOT"].$file);
    die();
?>