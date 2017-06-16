function repaint() {
	var screen_height = window.innerHeight;
	var navbar_height = $('#container-navbar').height();
	var main_height = screen_height - navbar_height;
	var main_width = window.innerWidth;

	var main_left_width = $('#container-main-left').width();
	var panel_height = main_height * 0.9;
	var panel_width = main_left_width * 0.8;
	var panel_margin_left = (main_left_width - panel_width) / 2;
	var panel_margin_top = (main_height - panel_height) / 2;

	var fb_height = panel_height / 3;
	var fb_width = panel_width - 10;
	var fb_margin_top;
	var fb_margin_left = 0.1;

	var btn_fb_login_desktop_width = fb_width * 0.8;
	var btn_fb_login_desktop_margin_left = (fb_width - btn_fb_login_desktop_width) / 2;
	var btn_fb_login_desktop_height;
	var btn_fb_login_desktop_margin_top;

	var welcome_height = panel_height * 2 / 9;
	var welcome_width = panel_width - 10;
	var welcome_margin_left = 0.1;
	var welcome_line_height = welcome_height;
	var welcome_font_size = window.innerWidth >= 768 ? window.innerWidth / 40 : window.innerWidth / 20; 

	var treasure_height = panel_height * 4 / 9;
	var treasure_width = panel_width - 10;
	var treasure_margin_left = 0.1;

	var input_digit_width = panel_width * 0.6;
	var input_digit_height = treasure_height * 0.2;
	var input_digit_margin_left = (panel_width - input_digit_width) / 2;
	var input_digit_margin_top = treasure_height * 0.15;
	//var input_digit_font_size = 30; //还需要进行计算

	var btn_treasure_width = panel_width * 0.6;
	var btn_treasure_height = treasure_height * 0.2;
	var btn_treasure_margin_left = (panel_width - input_digit_width) / 2;
	var btn_treasure_margin_top = treasure_height * 0.2; 
	//var btn_treasure_font_size = 30; //还需要进行计算

	var main_right_width = $('#container-main-right').width();
	var items_left_height = main_height * 0.8;
	var items_left_width = main_right_width/2;
	var items_right_height = main_height * 0.8;
	var items_right_width = main_right_width/2;
	var items_left_margin_top = (main_height - items_left_height) / 2;
	var items_right_margin_top = (main_height - items_right_height) / 2;

	var map_height = items_left_height / 2;
	var map_width = items_left_width;
	var map_img_height = map_height >= map_width ? map_width : map_height;
	map_img_height = map_img_height * 4 / 5;

	var rule_height = items_left_height / 2;
	var rule_width = items_left_width;
	var rule_img_height = rule_height >= rule_width ? rule_width : rule_height;
	rule_img_height = rule_img_height * 4 / 5;

	var purchase_height = items_right_height / 2;
	var purchase_width = items_right_width;
	var purchase_img_height = purchase_height >= purchase_width ? purchase_width : purchase_height;
	purchase_img_height = purchase_img_height * 4 / 5;

	var rank_height = items_right_height / 2;
	var rank_width = items_right_width;
	var rank_img_height = rank_height >= rank_width ? rank_width : rank_height;
	rank_img_height = rank_img_height * 4 / 5;

	/* main */
	$('#container-main').css('height', main_height +'px');

	/* left part */
	$('#container-main-left').css('height', main_height + 'px');
	/* login panel */
	$('#container-panel').css('height', panel_height + 'px');
	$('#container-panel').css('width', panel_width + 'px');
	$('#container-panel').css('margin-left', panel_margin_left + 'px');
	$('#container-panel').css('margin-top', panel_margin_top + 'px');
	/* fb panel */
	$('#container-fb-login').css('height', fb_height + 'px');
	$('#container-fb-login').css('width', fb_width + 'px');
	$('#container-fb-login').css('margin-left', fb_margin_left + 'px');
	/* fb login button */
	$('#btn-fb-login-desktop img').css('width', btn_fb_login_desktop_width + 'px');
	btn_fb_login_desktop_height = $('#btn-fb-login-desktop img').height();
	btn_fb_login_desktop_margin_top = (fb_height - btn_fb_login_desktop_height) / 2;
	$('#btn-fb-login-desktop img').css('margin-left', btn_fb_login_desktop_margin_left + 'px');
	$('#btn-fb-login-desktop img').css('margin-top', btn_fb_login_desktop_margin_top + 'px');
	$('#btn-fb-login-desktop img').hover(
		function() {
			$(this).css('opacity', 0.4);
		},
		function() {
			$(this).css('opacity', 1);
		}
	);
	/* digit input */
	$('#input-treasure-desktop').css('width', input_digit_width + 'px');
	$('#input-treasure-desktop').css('height', input_digit_height + 'px');
	$('#input-treasure-desktop').css('margin-left', input_digit_margin_left + 'px');
	$('#input-treasure-desktop').css('margin-top', input_digit_margin_top + 'px');
	//$('#input-treasure-desktop').css('font-size', input_digit_font_size + 'px');
	/* treasure take it button */
	$('#btn-treasure-desktop').css('width', btn_treasure_width + 'px');
	$('#btn-treasure-desktop').css('height', btn_treasure_height + 'px');
	$('#btn-treasure-desktop').css('margin-left', btn_treasure_margin_left + 'px');
	$('#btn-treasure-desktop').css('margin-top', btn_treasure_margin_top + 'px');
	//$('#btn-treasure-desktop').css('font-size', btn_treasure_font_size + 'px');

	/* treasure panel */
	$('#container-treasure').css('height', treasure_height + 'px');
	$('#container-treasure').css('width', treasure_width + 'px');
	$('#container-treasure').css('margin-left', treasure_margin_left + 'px');
	$('#container-treasure').addClass('disable-panel');
	/* welcome panel */
	$('#container-welcome').css('height', welcome_height + 'px');
	$('#container-welcome').css('width', welcome_width + 'px');
	$('#container-welcome').css('margin-left', welcome_margin_left + 'px');
	$('#container-welcome').css('line-height', welcome_line_height + 'px');
	$('#container-welcome p').css('line-height', welcome_line_height + 'px');
	$('#container-welcome p').css('text-align', 'center');
	/* welcome font size */
	$('#container-welcome p').css('font-size', welcome_font_size + 'px');

	/* right part */
	$('#container-main-right').css('height', main_height + 'px');
	/* img button hover */
	$('#container-main-right img').hover(
		function() {
			$(this).css('width', (map_img_height - 20) + 'px');
			$(this).css('height', (map_img_height - 20) + 'px');
		},
		function() {
			$(this).css('width', map_img_height + 'px');
			$(this).css('height', map_img_height + 'px');
		}
	);

	/* items left part */
	$('#container-items-left').css('height', items_left_height + 'px');
	$('#container-items-left').css('margin-top', items_left_margin_top + 'px');
	/* map */
	$('#container-map').css('height', map_height + 'px');
	$('#container-map').css('width', map_width + 'px');
	$('#container-map').css('text-align', 'center');
	$('#container-map').css('line-height', map_height + 'px');
	$('#container-map img').css('line-height', map_height + 'px');
	$('#container-map img').css('height', map_img_height + 'px');
	/* rules */
	$('#container-rules').css('height', rule_height + 'px');
	$('#container-rules').css('width', rule_width + 'px');
	$('#container-rules').css('text-align', 'center');
	$('#container-rules').css('line-height', rule_height + 'px');
	$('#container-rules img').css('line-height', rule_height + 'px');
	$('#container-rules img').css('height', rule_img_height + 'px');
	/* items right part */
	$('#container-items-right').css('height', items_right_height + 'px' );
	$('#container-items-right').css('margin-top', items_right_margin_top + 'px');
	/* purchase */
	$('#container-purchase').css('height', purchase_height + 'px');
	$('#container-purchase').css('width', purchase_width + 'px');
	$('#container-purchase').css('text-align', 'center');
	$('#container-purchase').css('line-height', purchase_height + 'px');
	$('#container-purchase img').css('line-height', purchase_height + 'px');
	$('#container-purchase img').css('height', purchase_img_height + 'px');
	/* rank */
	$('#container-rank').css('height', rank_height + 'px');
	$('#container-rank').css('width', rank_width + 'px');
	$('#container-rank').css('text-align', 'center');
	$('#container-rank').css('line-height', rank_height + 'px');
	$('#container-rank img').css('line-height', rank_height + 'px');
	$('#container-rank img').css('height', rank_img_height + 'px');

	/* dialog */
	$('#dialog').dialog({
		autoOpen: false,
	});
}