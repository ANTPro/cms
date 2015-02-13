<html>
	<head>
	    <title>[PAGE_TITLE]</title>
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	    <meta http-equiv="description" name="description" content="[PAGE_DESCRIPTION]" />
	    <link rel="stylesheet" type="text/css" href="[TPLDIR]default.css" />
			[META]
	    <script language="JavaScript" src="./functions.js"></script>
	</head>
	<body>
		<table width="100%" align="center" cellspacing="0" cellpadding="0">
			{
			<tr>
				<td align="center">
					<h1>
						<b>[SITE_TITLE]</b>
					</h1>
				</td>
			</tr>
			}
			{
			<tr height="5">
				<td></td>
				<td></td>
			</tr>
			[MAINMENU]
			}
			<tr height="5">
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<table class="content" width="100%" cellspacing="0" cellpadding="10">
						<th colspan="2">[MAIN_HEADER]</th>
						<tr>
							{
							<td valign="top" width="150">
								[MENU]
							</td>
							}
							<td valign="top">
								[MESSAGES]
								[CONTENT]
								<br />
								<a href="[SELF]&amp;print">Версия для печати</a>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" height="100%" width="3" class="shade">
					<table cellspacing="0" cellpadding="0" width="3" height="5" class="light">
						<tr>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="3" width="100%" class="shade">
					<table cellspacing="0" cellpadding="0" width="5" height="3" class="light">
						<tr>
							<td></td>
						</tr>
					</table>
				</td>
				<td class="shade">
				</td>
			</tr>
			<tr height="5">
				<td></td>
				<td></td>
			</tr>
			{
			<tr>
				<td align="center">[COPYRIGHT]</td>
			</tr>
			}
			{
			<tr>
				<td>Загруженно шаблонов: [TPLTEMPLATESCOUNT]<br />
				Использовано переменных: [TPLVARIABLESCOUNT]</td>
			</tr>
			}
			{
			<tr>
				<td align="center">[RUNTIME] сек.</td>
			</tr>
			}
		</table>
	</body>
</html>