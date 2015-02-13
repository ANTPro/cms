
<table class="text" width="100%" align="center" cellspacing="0" cellpadding="5">
	<form method="POST">
		<tr>
			<td width="100">Название статьи:</td><td><input name="title" type="text" value="[TITLE]" style="width:100%;"></td>
		</tr>
		<tr>
			<td width="100">Разместить в секции:</td>
			<td>
				<select name="id_section" style="width:100%;">
					[SECTIONS]
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>Описание:</td>
		</tr>
		<tr>
			<td colspan=2>
				<textarea name="description" rows="5" style="width:100%;">[DESCRIPTION]</textarea>
			</td>
		</tr>
		<tr>
			<td>Автор:</td><td>[AUTHOR]</td>
		</tr>
		<tr>
			<td>Дата публикации:</td><td>[PUBDATE]</td>
		</tr>
		<tr>
			<td colspan=2>[EDITOR]</td>
		</tr>
		<tr>
			<td colspan=2><input name="submit" type="submit" value="Сохранить изменения"></td>
		</tr>
	</form>
</table>