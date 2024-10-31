jQuery(document).ready(function($) {
	$('#next-post-btn').css("display", "block").click(function() {
		$('#next-post-btn').css("display", "none");
		$('#loader').css("display", "block");
		$.ajax({
			type : 'POST',
			url : PLG_Setting.ajaxurl,
			data : {
				action : PLG_Setting.action,
				page : PLG_Setting.next_page
			},
			timeout: 8000,
			error: function(){
				$('#next-post-btn').css("display", "block");
				$('#loader').css("display", "none");
				alert("データを取得できません");
			},
			success : function( data ) {
				PLG_Setting.next_page = data.next_page;
				var  table = createTable( data );
				$("#next-post-btn").before( table );
				$(".post-table:hidden").fadeIn("slow");
				$('#loader').css("display", "none");
				if ( data.has_next ){
					$('#next-post-btn').css("display", "block");
				}
			}
		});
		return false;
	});

	function createTable( info ){
		var items = info.items;
		var html = '<table "hidden" class="post-table">';
		var target = info.window_open ? 'target="_blank" ' : "";
		for ( var i = 0; i < items.length; i++){
			html += '<tr><td class="postdate">' + items[i].date + '</td>'
			+ '<td><a href="' + items[i].url + '"' + target + '>' + items[i].title + '</a></td></tr>';
		}
		html += '</table>';
		return html;
	}
});