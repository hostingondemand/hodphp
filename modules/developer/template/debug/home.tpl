<div class="options">
    <a href="{{url("","","clearCache")}}">{{language("options.clearCache")}}</a>
    {{if(isInDebug())}}
    <a href="{{url("","","toggleMode")}}">{{language("options.debugOff")}}  </a>
    <h2>debug options</h2>
    <a href="{{url("","","toggleClientCache")}}">
        {{if(isClientCacheOn())}}
        {{language("options.ClientCacheOff")}}
        {{else}}
        {{language("options.ClientCacheOn")}}
        {{/if}}
    </a>

    <a href="{{url("","","toggleStackTracing")}}">
        {{if(stackTraceOn)}}
        {{language("options.stacktraceOff")}}
        {{else}}
        {{language("options.stacktraceOn")}}
        {{/if}}
    </a>

    <a href="{{url("","","toggleProfiling")}}">
        {{if(profileOn)}}
        {{language("options.profileOff")}}
        {{else}}
        {{language("options.profileOn")}}
        {{/if}}
    </a>
    {{else}}
    <a href="{{url("","","toggleMode")}}">{{language("options.debugOn")}}  </a>
    {{/if}}
</div>