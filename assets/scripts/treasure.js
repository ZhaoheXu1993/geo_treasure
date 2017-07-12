function take_treasure(treasure_uuid) {
	if (treasure_uuid.length != 6) {
		return '406';
	} 

	var obj = new Object();
	obj.t_id = treasure_uuid;

	$.ajax({
		url: 'php/take_treasure.php',
		type: 'POST',
		data: JSON.stringify(obj),
		success: function(response_data) {
			/* return response obj */
			console.log(response_data);
		},
		error: function(xhr, status, data) {
			console.log(xhr.responseText);
		}
	});		
}