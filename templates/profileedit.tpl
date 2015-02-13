	<table class="text" width="100%" cellspacing="0" cellpadding="5">
		<form method="POST">
			<tr>
				<td>Ник:</td>
				<td>[PROFILE_LOGIN]</td>
			</tr>	
			<tr>
				<td>e-mail:</td>
				<td><input name="email" type="text" value="[PROFILE_EMAIL]" style="width:100%;"></td>
			</tr>
			<tr>
				<td>Имя:</td>
				<td><input name="name" type="text" value="[PROFILE_NAME]" style="width:100%;"></td>
			</tr>
			<tr>
				<td>Фамилия:</td>
				<td><input name="surname" type="text" value="[PROFILE_SURNAME]" style="width:100%;"></td>
			</tr>
			<tr>
				<td>Отчество:</td>
				<td><input name="patronymic" type="text" value="[PROFILE_PATRONYMIC]" style="width:100%;"></td>
			</tr>
			<tr>
				<td>Пол:</td>
				<td>
					<select name="sex" style="width:100%;">
						[SEX]
					</select>
				</td>
			</tr>
			<tr>
				<td>Оформление:</td>
				<td>
					<select name="id_themes" style="width:100%;">
						[THEMES]
					</select>
				</td>
			</tr>
			<tr>
				<td>Дата рождения:</td>
				<td>
					<select name="day">
						[DAYS]
					</select>
					<select name="month">
						[MONTHS]
					</select>
					<select name="year">
						[YEARS]
					</select>
				</td>
			</tr>
			<tr>
				<td colspan=2>Примечание:</td>
			</tr>
			<tr>
				<td colspan=2>
					<textarea name="note" cols="40" rows="5" style="width:100%;">[PROFILE_NOTE]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan=2 align="center">
					<input name="submit" type="submit" value="Сохранить">
				</td>
			</tr>
		</form>
	</table>