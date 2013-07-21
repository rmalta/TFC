
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
