{{foreach(result,currentTest,testName)}}
<h2>{{testName}}</h2>
<table>
    <thead>
    <td>{{language("column.title")}}</td>
    <td>{{language("column.comparison")}}</td>
    </thead>
    {{foreach(currentTest,currentAssert)}}
    {{foreach(currentAssert,currentTestResult)}}
    <tr>
        <td class="{{if(currentTestResult["success"])}}test-success{{else}}test-fail{{/if}}">{{currentTestResult["title"]}}</td>
        <td class="{{if(currentTestResult["success"])}}test-success{{else}}test-fail{{/if}}">{{currentTestResult["comparison"]}}</td>
    </tr>
    {{/foreach}}
    {{/foreach}}
</table>
{{/foreach}}