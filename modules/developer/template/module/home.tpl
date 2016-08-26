<table>
<tr>
    <Td>{{language("column.name")}}</Td>
    <Td>{{language("column.actions")}}</Td>
</tr>
{{foreach(modules,module)}}
    <tr>
        <td>{{module["name"]}}</td>
        <td>
            {{if(module["installed"])}}
                <a href="{{url("","","update",module["name"])}}">{{language("action.update")}}</a>
            {{else}}
                <a href="{{url("","","install",module["name"])}}">{{language("action.install")}}</a>
            {{/if}}
        </td>
    </tr>
{{/foreach}}
</table>