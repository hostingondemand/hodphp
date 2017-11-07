<select name="{{name}}" {{attributes}}>
    {{fordatasource(source,value)}}
    <option value="{{_value}}" {{if(_selected)}}selected{{/if}}>{{_text}}</option>
    {{/fordatasource}}
</select>
