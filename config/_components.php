<?php
return array(
    "provider.patchlog" => "file",
    "provider.cronlog" => "file",
    "provider.mapping" => "annotation",
    "provider.route" => "query",
    "provider.db" => "mysql",
    "provider.search"=>"db",
    "provider.dbModule"=>["parentModule"], //for backwards compatibility
    "provider.cache"=>"file"
);
