<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<title>Phalcon3 + php7 + mongodb</title>
{{ stylesheet_link("assets/css/uikit.min.css") }}
</head>
<body>

<div class="uk-container">

	<nav class="uk-navbar-container uk-margin-large-bottom" uk-navbar>
		<div class="uk-navbar-left">
		<ul class="uk-navbar-nav">
			<li>
				{{ link_to("/", 'Phalcon3 + php7 + mongodb', 'class':'uk-navbar-brand uk-hidden-small') }}
			</li>
			<li>
				{{ link_to("index/create", '投稿') }}
			</li>
		</ul>
		</div>
	</nav>


