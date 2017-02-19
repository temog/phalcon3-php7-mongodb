{% include 'header.volt' %}

{{ flashSession.output() }}

<h1 class="uk-heading-bullet uk-margin-bottom">編集</h1>

{{ partial('index/form') }}

{% include 'footer.volt' %}
