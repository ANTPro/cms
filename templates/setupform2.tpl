		<form method="POST">
			<input name="admin" type="hidden" value="[ADMIN]">
			<input name="pass" type="hidden" value="[PASS]">
			<table border=0 class="text" width="100%" cellspacing="0" cellpadding="5">
				<tr>
					<td width=10>
						<input name="execstruct" type="checkbox" value="ON">
					</td>
					<td>cоздать базу данных (struct.sql)</td>
				</tr>
				<tr>
					<td width=10>
						<input name="execinsert" type="checkbox" value="ON">
					</td>
					<td>заполнить базу тестовыми данными (insert.sql)</td>
				</tr>
				<tr>
					<td colspan=2>
						<input name="submitstep2" type="submit" value="Далее">
					</td>
				</tr>
			</table>
		</form>