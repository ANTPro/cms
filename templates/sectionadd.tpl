<table class="text" width="100%" align="center" cellspacing="0" cellpadding="5">
	<form action="?page=admin&tool=[TOOLNAME]&action=add" method="POST">
		<tr>
			<td width="150">Название раздела:</td>
			<td>
				<input name="title" type="text" value="[TITLE]" style="width:100%;">
			</td>
		</tr>
		<tr>
			<td width="150">Разместить в секции:</td>
			<td>
				<select name="id_section" style="width:100%;">
					<option selected>Выберите раздел</option>
					[SECTIONS]
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2><input name="submit" type="submit" value="Добавить"></td>
		</tr>
	</form>
</table>