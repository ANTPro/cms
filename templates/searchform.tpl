
			<form name="searchform" method="GET" action="?page=search" onsubmit="return searchsubmit()">				<input type="hidden" name="page" value="search" />
				<table class="text" width="100%" cellspacing="0" cellpadding="5">
					<tr>
						<td>	
							<input type="text" name="query" value="[SEARCH_TEXT]" onfocus="if(this.value=='Поиск...') this.value=''" onblur="if(this.value=='') this.value='Поиск...'" style="width:100%;">
						</td>
						<td width=100>
							<select name="location" style="width:100%;">
								[LOCATIONS]
							</select>
						</td>
						<td width=50>
							<input name="submit" type="submit" value="Найти" />
						</td>
					</tr>
				</table>
			</form>
			[TABLE]