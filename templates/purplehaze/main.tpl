<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>[PAGE_TITLE]</title>
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
		<meta http-equiv="description" name="description" content="[PAGE_DESCRIPTION]"/>
		<link rel="stylesheet" type="text/css" href="[TPLDIR]default.css"/>
		<script language="JavaScript" src="./functions.js"></script>
		[META]
	</head>
<body>
	<div id="header">
		{<span class="headerTitle">[SITE_TITLE]</span>}
		[MAINMENU]
	</div>

	{<div class="sideBox LHS">
		<div>Разделы</div>
		[MENU]
	</div>}

    <!--div class="sideBox RHS">
		
      <div>Left or Right</div>
      <span>
        These boxes can also appear on the right. Just replace the style
        <strong>LHS</strong> with <strong>RHS</strong>, and voil&agrave;!
      </span>
    </div>

    <div class="sideBox LHS">
      <div>Adding Boxes</div>
      <span>Adding menus and hint boxes is simple!</span>
    </div-->

    <div id="bodyText">
		<h1 id="introduction">[MAIN_HEADER]</h1>
		[MESSAGES]
		[CONTENT]
		<br/>
		<a href="[SELF]&print">Версия для печати<a>
	</div>
	
	<div>
		<div id="footer">
			<div>
				<strong>Copyright: </strong>
				[COPYRIGHT]
			</div>
			{<div>
				<strong>Время: </strong>
				<span class="footerCol2">[RUNTIME] сек.</span>
			</div>}
	    </div>
	</div>
  </body>
</html>