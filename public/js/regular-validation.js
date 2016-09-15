$(document).ready(function() {
	$(".unique").keyup(function() {
		if (this.value.match(/[^0-9a-zA-Z\?\;\,\!\-\.]/g)) {
			this.value = this.value.replace(/[^0-9a-zA-Z\?\;\,\!\-\.]\ /g, '');
		}
	});
	
			
	$(".number").keyup(function() {
		if (this.value.match(/[^0-9\,]/g)) {
			this.value = this.value.replace(/[^0-9\,]/g, '');
		}
	});
	
	$(".name").keyup(function() {
		if (this.value.match(/[^a-zA-Z]/g)) {
			this.value = this.value.replace(/[^a-zA-Z\ ]/g, '');
		}
	});
	$(".no-space").keyup(function() {
		if (this.value.match(/[^a-zA-Z]/g)) {
			this.value = this.value.replace(/^\s+/, "");
		}
	});

	$(".phone").keyup(function() {
		if (this.value.match(/[^0-9\-\(\)\+\ ]/g)) {
			this.value = this.value.replace(/[^0-9\-\(\)\+\ ]/g, '');
		}
	});
	
	$(".time").keyup(function() {
		if (this.value.match(/[^0-9]/g)) {
			this.value = this.value.replace(/[^0-9\:\ ]/g, '');
		}
	});

	$(".email").keyup(function() {
		if (this.value.match(/[^0-9a-zA-Z\b]/g)) {
			this.value = this.value.replace(/[^0-9a-zA-Z\_\@\-\.]/g, '');
		}
	});

	$("#confirmpassword").keyup(function() {
		if (this.value.match(/[^0-9a-zA-Z]/g)) {
			this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
		}
	});
	$(".mobile").keyup(function() {
		if (this.value.match(/[^0-9]/g)) {
			this.value = this.value.replace(/[^0-9]/g, '');
		}
	});
	$(".describe").keyup(function() {
		if (this.value.match(/[^a-zA-Z\-\/\(\)\+\b]/g)) {
			this.value = this.value.replace(/[^a-zA-Z\-\/\(\)\+\.\ ]/g, '');
		}
	});
	$(".shortname").keyup(function() {
		if (this.value.match(/[0-9a-zA-Z]/g)) {
			this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
		}
	});
});
