{{for(i,substract(count,1))}}
{{if(i==substract(current,1))}}
<a href="{{urlParameter("page",sum(i,1))}}"><strong>{{sum(i,1)}}</strong></a>
{{else}}
<a href="{{urlParameter("page",sum(i,1))}}">{{sum(i,1)}}</a>
{{/if}}
{{/for}}