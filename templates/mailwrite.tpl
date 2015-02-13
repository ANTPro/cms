
	<table class="text" width="100%" cellspacing="0" cellpadding="5">
		<form method="POST">
			<tr>
				<td width=50>Кому:</td>
				<td>
					<input name="sendto" type="text" value="[SENDTO]" style="width:100%;">
				</td>
				<td width=100>
					<select name="group">
						<option value="1" selected>пользователю</option>
						<option value="2">группе</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width=50>Тема:</td>
				<td colspan=2>
					<input name="title" type="text" value="[TITLE]" style="width:100%;">
				</td>
			</tr>
			<tr>
				<td colspan=3>Текст сообщения:</td>
			</tr>			
			<tr>
				<td colspan=3 align="center">
					<textarea name="message" cols="40" rows="5" style="width:100%;">[MESSAGE]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan=3 align="center">
					<input name="submit" type="submit" value="Отправить">
				</td>
			</tr>
		</form>
	</table>