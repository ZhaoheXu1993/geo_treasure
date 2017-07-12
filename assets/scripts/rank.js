function abbreviate_name(name) {
	var w = $('.table-content').width() - 30; // 30 is image width
	if (name.length >= w / 10) {
		return name.substring(0, w/10 - 1) + '...';
	} else {
		return name;
	}
}

function get_rank(rank_name) {
	var obj = new Object();
	obj.rank_name = rank_name;

	$.ajax({
		url: 'php/get_rank.php',
		type: 'POST',
		data: JSON.stringify(obj),
		success: function(response_data) {
			console.log(response_data);
			if (response_data.top_rank) {
				var ranks = response_data.top_rank;
				var table = document.getElementById('table-top-ranks');
				
				for (var i = 1; i <= (ranks.length > 10 ? 10 : ranks.length); i++) {
					table.children[i-1].innerHTML = "<tr><td class='table-digit'>" 
					+ i 
					+ "</td><td class='table-content'>"
					+ "<img src='"
					+ ranks[i].img_url + "'>"
					+ abbreviate_name(ranks[i].name) + "</td><td class='table-digit'>"
					+ ranks[i].value + "</td></tr>";
				}
			}

			if (response_data.user_rank) {
				var rank = response_data.user_rank;

				$('#table-user-ranks').html("<tr><td class='table-digit'>" 
						+ rank[0].rank + "</td><td class='table-content'>"
						+ "<img src='" 
						+ rank[0].img_url + "'>"
						+ rank[0].name + "</td><td class='table-digit'>"
						+ rank[0].value + "</td></tr>"); 
			}

			repaint();
		},
		error: function(xhr, status, data) {
			console.log(xhr.responseText);
		}
	});	
}