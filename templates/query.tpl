	<table class="text" width="100%" cellspacing="0" cellpadding="5">
		<form method="GET">
			<input type="hidden" name="page" value="admin" />
			<input type="hidden" name="tool" value="[TOOLNAME]" />
			<input type="hidden" name="action" value="exec" />
			<tr>
				<td>Текст запроса:</td>
			</tr>			
			<tr>
				<td>
					<textarea name="query" rows="10" style="width:100%;">[QUERY]</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input name="submit" type="submit" value="Выполнить">
				</td>
			</tr>
		</form>
	</table>