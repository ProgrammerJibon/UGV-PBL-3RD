<?php header("content-type: text/javascript"); ?>
/* <script type="text/javascript">/**/
function loadLink(url, data){
	// loadLink('/pages.php', [['name','jibon'],['bool','false']]).then(result=>{console.log(result)})
	return new Promise(function(resolve, reject){

		var http = new XMLHttpRequest();
		http.open("POST", url);
		var formData = new FormData();
		if (data != null) {
			data = [...data];
			data.forEach((post)=>{
			  if (post[0] && post[1]) {
			  	formData.append(post[0], post[1]);
			  }
			})
		}
		http.send(formData);
		http.onload=()=>{
			resolve(JSON.parse(http.responseText));
		}
	});
}
function forceDownload(href) {

	var anchor = document.createElement('a');

	anchor.href = href;

	anchor.download = href;

	document.body.appendChild(anchor);

	anchor.click();

	document.body.removeChild(anchor);

}
function viewToggle(div) {

	div.classList.toggle("show")

}

function viewRemove(div) {

	div.classList.remove("show")

}

const validateEmail = (email) => {

	return String(email)

	.toLowerCase()

	.match(

		/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

	);

};




function create(name, classes = null, id = null){

	if(name != "" && name != null && name != undefined && name != false){

		var element = document.createElement(name);

		if(classes != "" && classes != null && classes != undefined && classes != false){

			classes.split(" ").forEach(item=>{

				if(item != ""){

					element.classList.add(item);

				}				

			});

		}

		if(id != "" && id != null && id != undefined && id != false){

			id.split(" ").forEach(item=>{

				if(item != ""){

					element.id += (" ")+(item);

				}				

			});

		}

		return element;

	}else{

		return false;

	}

}





function byId(id){

	if (document.getElementById(id)) {

		return document.getElementById(id);

	}else{

		return false;

	}

}








function rgba(r, g, b, a){

	if (r < 0 || r > 255) {

		r = 0;

	}

	if (g < 0 || g > 255) {

		g = 0;

	}

	if (b < 0 || b > 255) {

		b = 0;

	}

	if (a < 0 || a > 1) {

		a = 1;

	}

	var data = "rgba("+r+", "+g+", "+b+", "+a+")";

	return data;

}






function notification(text, color){

	if (document.getElementById("event_5")) {

		var view = document.getElementById("event_5");

		var newDiv = document.createElement("div");

		newDiv.classList.add("notification");

		newDiv.innerHTML = text;

		view.appendChild(newDiv);

		newDiv.onclick=e=>{

			setTimeout(v=>{

				newDiv.style = "";

			}, 100);

			setTimeout(v=>{

				newDiv.remove();

			}, 300);

		}

		setTimeout(v=>{

			newDiv.style = "";

		}, 20000);

		setTimeout(v=>{

			newDiv.remove();

		}, 20300);

		var newInterval = setInterval(e=>{

			colorx = rgba(Math.floor(Math.random()*255+1),Math.floor(Math.random()*255+1),Math.floor(Math.random()*255+1), 1);

			newDiv.style = "color:"+colorx+";padding: 8px 16px;height: 35px;border: 1px solid;opacity: 1;border-radius: 3px;font-size: 11px;margin-bottom: 4px;";

		},250);



		setTimeout(v=>{

			newDiv.style = "color:"+color+";padding: 8px 16px;height: 35px;border: 1px solid;opacity: 1;border-radius: 3px;font-size: 11px;margin-bottom: 4px;";

			clearInterval(newInterval);

		}, 3000);

	}else{

		var event_5 = document.createElement("div");

		event_5.id = "event_5";

		document.querySelector("body").appendChild(event_5);

		notification(text, color);

	}

}





function previewInputImage(input, image) {
  var input = input.files[0];
  var reader = new FileReader();

  reader.onload = function () {
	console.log(image);
    image.src = reader.result;
	image.style.display = "block";
  }

  if (input) {
    reader.readAsDataURL(input);
  } else {
    image.src = "";
	image.style.display = "none";
  }
}










function href(link){

	if(link){

		window.location.href = (link);

	}

}

function tab(link){

	if(link){

		window.open(link);

	}

}


window.onload = () =>window_onload;




function window_onload(){
		
}