function searchsubmit()
{
	var v=document.forms['searchform'].elements['query'].value;
	return (v!='') && (v!='Поиск...');
}
function subm(ids)
{
	if (ids.style.display == 'block')
	{
		ids.style.display='none';
	}
	else
	{
		ids.style.display='block';
	}
}
function msup(id)
{
	id.className='umenu';
}
function msout(id)
{
	id.className='dmenu';
}