function repaint() {
	var h = $('td').height();
	$('.table-digit').css('line-height', h + 'px');
	$('.table-content').css('line-height', h + 'px');
}