<?php
{{foreach(classes,class)}}
    namespace {{class["namespace"]}};
    abstract class {{class["name"]}} {{if(class["extends"])}} extends {{class["extends"]}} {{/if}} {
    {{foreach(class["properties"],property)}}
    /**
    * @var {{property["cls"]}}
    */
    var ${{property["name"]}};
    {{/foreach}}
     {{foreach(class["methods"],method)}}
    {{if(method["returnType"])}}
    /**
    * @return {{method["returnType"]}}
    */
    {{/if}}
   {{if(method["isAbstract"])}}abstract{{/if}} function {{method["name"]}}({{method["params"]}}){{if(method["isAbstract"])}};{{else}}{}{{/if}}
    {{/foreach}}
    }
{{/foreach}}