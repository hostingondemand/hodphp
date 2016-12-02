<form action="{{url("","","install")}}" method="post">
    <h2>{{language("title.home")}}</h2>
    <p><label>{{language("field.git")}}</label> {{inputFor("git","string")}}</p>
    <p><label>{{language("field.dbDb")}}</label> {{inputFor("dbDb","string")}}</p>
    <p><label>{{language("field.dbHost")}}</label> {{inputFor("dbHost","string")}}</p>
    <p><label>{{language("field.dbUser")}}</label> {{inputFor("dbUser","string")}}</p>
    <p><label>{{language("field.dbPassword")}}</label> {{inputFor("dbPassword","string")}}</p>
    <input type="submit" value="{{language("action.install")}}">
</form>
