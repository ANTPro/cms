<table class="text" width="100%" align="center" cellspacing="0" cellpadding="5">
	<form method="POST">
		<input type=hidden name="choise" value="">
		<tr>
			<td width="150">Название группы:</td>
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
				<select onchange="this.form.choise.value='department';this.form.submit();" name="id_department" style="width:100%;">
					[DEPARTMENT]
				</select>
			</td>
		</tr>
		<tr>
			<td width="150">Специальность:</td>
			<td>
				<select name="id_specialization" style="width:100%;">
					[SPECIALIZATION]
				</select>
			</td>
		</tr>
		<tr>
			<td width="150">Курс:</td>
			<td>
				<input name="year" type="text" value="[YEAR]" style="width:100%;">
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<input name="submitdata" type="submit" value="Добавить">
			</td>
		</tr>
	</form>
</table>