<table class="text" width="100%" align="center" cellspacing="0" cellpadding="5">
	<form method="POST">
		<input type=hidden name="choise" value="">
		<tr>
			<td width="150">Название:</td>
			<td>
				<input name="title" type="text" value="[TITLE]" style="width:100%;">
			</td>
		</tr>
		<tr>
			<td width="150">Факультет:</td>
			<td>
				<select onchange="this.form.choise.value='faculty';this.form.submit();" name="id_faculty" style="width:100%;">
					[FACULTY]
				</select>
			</td>
		</tr>
		<tr>
			<td width="150">Кафедра:</td>
			<td>
				<select name="id_department" style="width:100%;">
					[DEPARTMENT]
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<input name="submitdata" type="submit" value="Сохранить изминения">
			</td>
		</tr>
	</form>
</table>