var breadCrumbStartSet;
function initializePage(){
	if(document.getElementById('breadcrumbP').innerHTML.substring(0,59)=='<a class="hidden-a" href="?p=index">PHPDev Forums</a> &gt; '){
		console.log("Was al gezet");
		breadCrumbStartSet = 1;
	} else {
		breadCrumbStartSet = 0;
	}
}
function resizeBreadCrumb(){
	document.getElementById('breadcrumbP').style.width = 0;
	var width = 0;
	width = document.getElementById('header-table').clientWidth - 66;
	document.getElementById('breadcrumbP').style.width = width + "px";
	
	var index = '<a class="hidden-a" href="?p=index">PHPDev Forums</a> > ';
	var orig = document.getElementById('breadcrumbP').innerHTML;
	if((window.innerWidth > 750) && (breadCrumbStartSet == 0)){ 
		breadCrumbStartSet = 1;
		console.log('Set text');
		document.getElementById('breadcrumbP').innerHTML = index.concat(orig); 
	} else if((window.innerWidth <= 750) && (breadCrumbStartSet == 1)){ 
		breadCrumbStartSet = 0;
		console.log('Unset text');
		document.getElementById('breadcrumbP').innerHTML = orig.substring(59); 
	}	
	
	setCookie("windowWidth", window.innerWidth, 3600);
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}