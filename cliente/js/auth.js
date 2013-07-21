
// Force to support Phone Gap http://jquerymobile.com/demos/1.2.1/docs/pages/phonegap.html

$.support.cors = true;

var tipo_cliente = "taxi";

$(document).ready(function() {
	
	// observe submit button
	$('#submitButton').click(login_local);
	
	$('#logout').click(logout_local);

	if(typeof(initEstado) != "undefined")
		initEstado();

	if(localStorage.getItem("username") === null && window.location.href.indexOf("index.html") === -1)
		window.location.href = "index.html";

});

function login_local () {
	
	login($('#username').val(), $('#password').val());
}

function logout_local () {

	logout($('#username').val());
}

function logout_facebook (response) {

	console.log("facebook logout");

	FB.api('/me', function(response) {
    	logout(response.username);
	});
}

function login(username, password){
		
	$.ajax({

		type: "POST",
		url: "http://tfctaxi.hostzi.com/login.php",
		data: { uname: username, passwd: password },
		dataType: 'json',
		success: function(res){

			// save token
			
			if (res.result === "authorized") {
				
				localStorage.setItem("token", res.token);
				localStorage.setItem("username", username);
				
				window.location.href = "app.html";
			}
			else{
				$('#error').text("Nome de utilizador ou password errados!");
			}

		},
		error: function(res, textStatus, errorThrown ){
			
			if (res && res.status === 302) {
				window.location.href = request.getResponseHeader('Location');
			}

			window.location.href = "index.html";
		}
	});	
}

function logout(username){

	$.ajax({

		type: "POST",
		url: "http://tfctaxi.hostzi.com/logout.php",
		data: {uname: username},
		dataType: 'json',
		success: function(res){

			if(localStorage.getItem("facebook")){
				FB.logout(function(response) {
			        
			        localStorage.clear();
					window.location.href = "index.html";
			    });
			}
			else{
				
				localStorage.clear();
				window.location.href = "index.html";
			}
		},
		error: function(res, textStatus, errorThrown ){
			
			if (res && res.status === 302) {
				window.location.href = request.getResponseHeader('Location');
			}

			window.location.href = "index.html";
		}
	});
}

$(window).resize(function(){
	var height = $(document).height() - ($("#header").height() + $("#footer").height() + 5);
	var map = $('#map');
	if (map) {
		map.height(height);
	}
	else{
		$('#content').height(height);
	}
})

function adjustMap(){
	var height = $(document).height() - ($("#header").height() + $("#footer").height() + 5);
	var map = $('#map');
	if (map) {
		map.height(height);
	}
	else{
		$('#content').height(height);
	}
}


window.fbAsyncInit = function() {
	
	FB.init({
	  appId      : '588390807850802', // App ID
	  channelUrl : '//tfctaxi.hostzi.com/cliente/index.html', // Channel File
	  status     : true, // check login status
	  cookie     : true, // enable cookies to allow the server to access the session
	  xfbml      : true  // parse XFBML
	});


	FB.Event.subscribe('auth.logout', logout_facebook);
  
	FB.Event.subscribe('auth.authResponseChange', function(response) {
    
		if (response.status === 'connected') {

				var ftoken = response.authResponse.accessToken;

				if(localStorage.getItem("facebook"))
					return;
		  
			    FB.api('/me', function(response) {
				      
				      var vals = { 	uname: response.username || "", passwd: ftoken };
				      
				      $.ajax({

					      type: "POST",
					      url: "http://tfctaxi.hostzi.com/facebookauth.php",
					      data: vals,
					      dataType: 'json',
					      success: function(res){
						      
						      	// save token

						      	localStorage.setItem("facebook", res.token);
								localStorage.setItem("token", res.token);
								localStorage.setItem("username", vals.uname);

								window.location.href = "app.html";

					      },
					      error: function(jqXHR, textStatus, errorThrown ){
						      console.log(arguments);
						      console.log("fail: ", jqXHR, textStatus, errorThrown);

						      window.location.href = "index.html";
					      }
				      });
			      });
		  
		} else if (response.status === 'not_authorized') {
		  // In this case, the person is logged into Facebook, but not into the app, so we call
		  // FB.login() to prompt them to do so. 
		  // In real-life usage, you wouldn't want to immediately prompt someone to login 
		  // like this, for two reasons:
		  // (1) JavaScript created popup windows are blocked by most browsers unless they 
		  // result from direct interaction from people using the app (such as a mouse click)
		  // (2) it is a bad experience to be continually prompted to login upon page load.
		  FB.login();
		} else {
		  // In this case, the person is not logged into Facebook, so we call the login() 
		  // function to prompt them to do so. Note that at this stage there is no indication
		  // of whether they are logged into the app. If they aren't then they'll see the Login
		  // dialog right after they log in to Facebook. 
		  // The same caveats as above apply to the FB.login() call here.
		  FB.login();
		}
	});
  };

  // Load the SDK asynchronously
  (function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
  }(document));
