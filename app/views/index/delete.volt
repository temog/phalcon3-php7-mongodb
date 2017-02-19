{% include 'header.volt' %}

{{ flashSession.output() }}

<h1 class="uk-heading-bullet uk-margin-bottom">削除</h1>

<div class="uk-alert-warning" uk-alert>
	<p>削除しますか</p>
</div>

<article class="uk-article uk-margin-large-bottom">

	<h1 class="uk-article-title">{{ blog.title|e }}</h1>

	<p class="uk-article-meta">written on {{ date("Y-m-d H:i:s", blog.created_at) }}</p>

	<p class="uk-text-lead">{{ blog.body|e|nl2br }}</p>

</article>


{{ form(null, 'method':'post') }}

	{{ hidden_field(this.security.getTokenKey(), 'value': this.security.getToken()) }}
	<button type="submit" class="uk-button uk-button-danger">削除</button>

{{ end_form() }}

{% include 'footer.volt' %}

