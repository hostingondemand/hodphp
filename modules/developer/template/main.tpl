<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="{{content("style.css")}}">
</head>

<body>
<div class="header">
    <h1>Developer console</h1>
</div>
<div class="menu">
    <ul>
        <li><a href="{{url("","install","options")}}">{{language("menu.updateManager")}}</a></li>
        <li><a href="{{url("","debug")}}">{{language("menu.debug")}}</a></li>
        <li><a href="{{url("","test")}}">{{language("menu.test")}}</a></li>
    </ul>
</div>

<div class="messages">
    {{foreach(getMessages(),message)}}
    <div class="message {{message["type"]}}">
        {{message["message"]}}
    </div>
    {{/foreach}}
</div>

<div class="content">
    {{content}}
    <div style="clear:both"></div>
</div>

<div class="footer">
    {{language("footer")}}
</div>
</body>
</html>
