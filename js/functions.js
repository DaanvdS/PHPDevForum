function resizeBreadCrumb(){
	document.getElementById('breadcrumbP').style.width = 0;
	var width = 0;
	width = document.getElementById('header-table').clientWidth - 66;
	document.getElementById('breadcrumbP').style.width = width + "px";
	console.log(width);
	
	var index = '<a class="hidden-a" href="?p=index">PHPDev Forums</a> > ';
	var orig = document.getElementById('breadcrumbP').innerHTML;
	if(window.innerWidth > 750 && breadCrumbStartSet = 0){ 
		breadCrumbStartSet = 1;
		console.log('Set text');
		document.getElementById('breadcrumbP').innerHTML = index.concat(orig); 
	}
}
function initializePage(){
	var breadCrumbStartSet = 0;
}