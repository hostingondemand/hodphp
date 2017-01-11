<div class="options">
    <a href="{{url("","","clearCache")}}">{{language("options.clearCache")}}</a>

    <a href="{{url("","","toggleMode")}}">
        {{if(isInDebug())}}
        {{language("options.debugOff")}}
        {{else}}
        {{language("options.debugOn")}}
        {{/if}}
    </a>


    <a href="{{url("","","toggleClientCache")}}">
        {{if(isClientCacheOn())}}
        {{language("options.ClientCacheOff")}}
        {{else}}
        {{language("options.ClientCacheOn")}}
        {{/if}}
    </a>
</div>