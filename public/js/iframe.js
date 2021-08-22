function myFunction(css_id, bill_url) {
	const container = document.getElementById(css_id)
	const iframe = document.createElement("iframe");
	iframe.setAttribute("src", bill_url);
	iframe.setAttribute("id", "frame");
	iframe.style.width = "640px";
	iframe.style.height = "480px";
	container.appendChild(iframe);
	document.getElementById('frame').addEventListener( "load", function(e) {

				    console.log(e.target.src);
				    console.log('d');
				    console.log(document.getElementById("frame").contentDocument.referrer);
				    console.log('do');
		var src_url = e.target.src;
		if(bill_url != src_url){
		    this.style.backgroundColor = "red";
		    alert(this.nodeName);
		    console.log(e.target.src);
		}
	} );
}