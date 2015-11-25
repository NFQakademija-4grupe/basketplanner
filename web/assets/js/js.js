var isCollapsed = false;

function collapse(){
	if(isCollapsed){
		document.getElementById('body').className =
	    document.getElementById('body').className.replace( /(?:^|\s)aside-collapsed(?!\S)/g , '' );
		document.getElementById('sidebar_left').style.width = "";
		document.getElementById('sidebar').style.width = "";
	}
	else{
		document.getElementById('body').className += " aside-collapsed";
		document.getElementById('sidebar_left').style.width = "70px";
		document.getElementById('sidebar').style.width = "70px";
	}
	isCollapsed = !isCollapsed;
}