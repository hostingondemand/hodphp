<table>
    <tr>
        <thead>
        <Td>{{language("column.name")}}</Td>
        <Td>{{language("column.actions")}}</Td>
        </thead>
    </tr>
    {{foreach(modules,module)}}
    <tr>
        <td>{{module["name"]}}</td>
        <td>
            {{if(module["installed"])}}
            <a href="{{url("","","update",module["name"])}}" class="button">{{language("action.update")}}</a>
            {{else}}
            <a href="{{url("","","install",module["name"])}}" class="button">{{language("action.install")}}</a>
            {{/if}}
        </td>
    </tr>
    {{/foreach}}
</table>

