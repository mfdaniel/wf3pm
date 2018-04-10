function validate(userName) {
	$.post(
		'/username/available',
		{
			username: userName
		},
		function(responseData) {
			$('.username-validation').remove();
			
			if (responseData.available) {
				$('label[for="form_username"]').append(
					'<span style="border:2px solid green" class="username-available username-validation"> available</span>'
				);
				
				return;
			}

			$('label[for="form_username"]').append(
				'<span style="border:2px solid red" class="username-unavailable username-validation"> unavailable</span>'
			);
		}
	);
}

$('#form_username').on('keyup', function(){
	validate($(this).val());
});