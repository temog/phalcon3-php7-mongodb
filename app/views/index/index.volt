{% include 'header.volt' %}

{{ flashSession.output() }}

<h1 class="uk-heading-bullet uk-margin-bottom">Blog 一覧</h1>

{% if ! latest %}

	<div class="uk-alert-warning" uk-alert>
		<p>投稿がありません</p>
	</div>

{% else %}

	<table class="uk-table">
		<thead>
			<tr>
				<th>タイトル</th>
				<th>日時</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>

		{% for blog in latest %}

			<tr>
				<td>{{ blog.title|e }}</td>
				<td>{{ date("Y-m-d H:i:s", blog.created_at) }}</td>
				<td>
					{{ link_to('index/edit/' ~ blog._id, 'Edit',
						'class':'uk-button uk-button-default uk-button-small') }}

					{{ link_to('index/delete/' ~ blog._id, 'Delete',
						'class':'uk-button uk-button-danger uk-button-small') }}
				</td>
			</tr>

		{% endfor %}

		</tbody>
	</table>

	{% if paginator %}
	{{ paginator }}
	{% endif %}

{% endif %}


{% include 'footer.volt' %}

