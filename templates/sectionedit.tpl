<table class="text" width="100%" align="center" cellspacing="0" cellpadding="5">
	<form action="?page=admin&tool=[TOOLNAME]&action=edit&id=[ID]" method="POST">
		<tr>
			<td width="150">Название раздела:</td>
			<td><input name="title" type="text" value="[TITLE]" style="width:100%;"></td>
		</tr>
		<tr>
			<td width="150">Разместить в секции:</td>
			<td>
				<select name="id_section" style="width:100%;">
					<option value="0">Нет</option>
					[SECTIONS]
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2><input name="submit" type="submit" value="Сохранить изменения"></td>
		</tr>
	</form>
</table>