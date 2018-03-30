function jbutton_change(jbutton)
{ try{
	var jbutton_width = $('#' + jbutton).outerWidth();
	$('#' + jbutton).addClass('button_change').attr('disabled', 'disabled');
	if(!isNaN(jbutton_width)) { $('#' + jbutton).width(jbutton_width); }
} catch(e){} return true; }