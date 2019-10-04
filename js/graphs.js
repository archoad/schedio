

function loadGantt() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			displayGantt(this.responseText);
		}
	};
	xhttp.open('POST', 'graphs.php', true);
	xhttp.send();
}


function displayGantt(datas) {
	var jsonObj = JSON.parse(datas);
	var container = document.getElementById('ganttGraph');
	var items = new vis.DataSet(jsonObj.tasks);
	var groups = new vis.DataSet(jsonObj.groups);
	var options = {
		locale: 'fr_FR',
		zoomable: false,
		tooltip: { followMouse: true, delay: 100 },
		timeAxis: { scale: 'day', step: 1 },
		format: { minorLabels: {day: 'D'} },
		showCurrentTime: true,
		editable: false,
		groupOrder: 'id',
		visibleFrameTemplate: function(item) {
			if (item.visibleFrameTemplate) {
				return item.visibleFrameTemplate;
			}
			return "<div class='gantt_container'><div class='gantt_progress' style='width: " + item.value + "'>" + item.value + "</div></div>";
		}
	};
	var timeline = new vis.Timeline(container, items, groups, options);
}
