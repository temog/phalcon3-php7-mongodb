{{ form(null, 'method':'post') }}

	{{ hidden_field(this.security.getTokenKey(), 'value': this.security.getToken()) }}

	<fieldset class="uk-fieldset">

		<div class="uk-margin">
			{{ text_field('title', 'class':'uk-input', 'placeholder':'タイトル') }}
		</div>

		<div class="uk-margin">
			{{ text_area('body', 'class':'uk-textarea', 'rows':5, 'placeholder':'本文') }}
		</div>

		<div class="uk-margin">
			<button class="uk-button uk-button-primary">投稿</button>
		</div>

	</fieldset>

{{ end_form() }}

