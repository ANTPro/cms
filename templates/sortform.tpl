
			<form name="sortform" method="POST">
				<table class="text" cellspacing="0" cellpadding="2">
					<tr>
						<td>Сортировать по полю:</td>					
						<td>
							<select name="fields">				
								[SORTFIELDS]
							</select>
						</td>
						<td>
							<input name="desc" type="checkbox" value="ON"[SORTTYPE]> по убыванию
						</td>
						<td></td>	
						<td>Страница: </td>	
						<td>
							<select name="showpage">				
								[PAGES]
							</select>
						</td>
						<td>
							<input name="sortsubmit" type="submit" value="Обновить" />
						</td>
					</tr>
				</table>
			</form>
