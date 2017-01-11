<html>
<head>
    <title></title>
    <content
</head>

<body>
<h1>Developer console</h1>
<div class="menu">
    <a href="{{url("","module")}}">{{language("menu.modules")}}</a>

    <a href="{{url("","install","update")}}">{{language("menu.update")}}</a>
    <a href="{{url("","install","clearCache")}}">{{language("menu.clearCache")}}</a>
    <a href="{{url("","debugMode","toggle")}}">
        {{if(isInDebug())}}
            {{language("menu.debugOff")}}
        {{else}}
            {{language("menu.debugOn")}}
        {{/if}}
    </a>
</div>
<hr />
{{foreach(getMessages(),message)}}
{{message["type"]}}:{{message["message"]}} <br />
{{/foreach}}
<hr />

{{content}}
</body>
</html>
