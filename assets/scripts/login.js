function guide() {

}

function enable_treasure() {
	if ($('#container-treasure').hasClass('disable-panel')) {
		$('#container-treasure').removeClass('disable-panel');
	}
}

function statusChangeCallback(response) {
	if (response.status === 'connected') {
		FB.login(
			function(response) {
			  console.log('FB.login with permissions callback', response);
			},
			{ scope: 'publish_actions' }
		);
		create_user();

		/* enable treasure */
		enable_treasure();

		/* user guide */
		guide();
	} else {
		FB.login(function(response) {
		    if (response.authResponse) {
				console.log('Welcome!  Fetching your information.... ');

				create_user();

				/* enable treasure */
				enable_treasure();

				/* user guide */
				guide();
		    } else {
				console.log('User cancelled login or did not fully authorize.');
		    }
		});
	}
}

function checkLoginState() {
	FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
	});
}

window.fbAsyncInit = function() {
	FB.init({
		appId: '730751350421123',
		cookie: true, // enable cookies to allow the server to access the session
		xfbml: true, // parse social plugins on this page
		status: true,
		version: 'v2.8' // use graph api version 2.8
	});

	FB.Event.subscribe('auth.login', function(response) {
		console.log('auth.login event handler', response);
	});
	FB.Event.subscribe('auth.logout', function(response) {
		console.log('auth.logout event handler', response);
	});
	FB.Event.subscribe('auth.statusChange', function(response) {
		console.log('auth.statusChange event handler', response);
	});
	FB.Event.subscribe('auth.authResponseChange', function(response) {
		console.log('auth.authResponseChange event handler', response);
	});

	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			var btn = document.getElementById('btn-fb-login-desktop');
			btn.childNodes[1].src = "https://treasurehuntingpro.com/assets/images/fb-continue.png";

			create_user();

			/* enable treasure */
			enable_treasure();

			/* user guide */
			guide();
		}
	});
};

// Load the SDK asynchronously
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=730751350421123";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function create_user() {
    var obj = new Object();
	FB.api('/me?fields=name,email,link,picture', function(response) {
		//console.log(response);
		obj.name = response.name;
		obj.email = response.email;
		obj.link = response.link;
		obj.fb_id = response.id;
		obj.img_url = response.picture.data.url;
		$.ajax({
			url: 'php/create_user.php',
			type: 'POST',
			data: JSON.stringify(obj),
			success: function(response_data) {
				user_uuid = response_data.user_uuid;
			},
			error: function(xhr, status, data) {
				console.log(xhr.responseText);
			}
		});		
	});	
}