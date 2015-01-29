function resizeBreadCrumb(){
	document.getElementById('breadcrumbP').style.width = 0;
	var width = 0;
	width = document.getElementById('header-table').clientWidth - 66;
	document.getElementById('breadcrumbP').style.width = width + "px";
	console.log(width);
}