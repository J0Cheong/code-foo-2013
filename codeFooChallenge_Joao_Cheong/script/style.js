//4. Create a responsive layout using media queries. Must support iPad, iPhone, and common resolutions for desktops. Nest your entire application in this responsive interface.
/*
Resolutions (Horz x Vert/Landscape):

IPAD: 1024x768 w/ Retina

Iphone (accounting for browser): 
	[4 & below]: 480x320 w/Retina
	[5 & up]: 568x320 w/Retina 

Desktop:
	1024x768
	1366x768
	1280x800
	1280x1024
*/
//this function bracket around json forces json to be a local variable. Here are the benefits:
// variables inside the function do not need to be initialized, but they must be available when the function is called
// variables change dynamically in the json object, if it was not in a function, the variable would get dereferenced as soon as it sees the first declared reference.

/* Here is an example:
	var message;
	var json= {"hello": message};
	message= "world";
	console.log(json); //{hello: "world"}
	message= "IGN";
	console.log(json); //{hello: "world"}
*/

// alternative would have been to wrap the json in another json with brackets. Meaning you have a dozen sets of json just like you have "several different css files depending on screen resolution" In other words, too much repetition that simple scoping would fix 

function css(){
	var json = {
		"#nav": {
			"li": {
				"display": "inline-block",
				"list-styles": "none",
				"margin-right": "10px",
				"padding": screenPad
			}
		},
		"body" : {
			"background": "#303030",
			"color": "#FFFFFF",
			"font-family": "Arial, Helvetica, sans-serif",
			"width": "100%"			
		},
		"h2": {
			"font-size": (1.4*screenFont),
			"text-indent": "10px"
		},
		"a": {
			"color": "#FFFFFF",
            "font-size": (1.4*screenFont),
			"text-decoration": "none",
			"text-shadow": "1px 1px #FF0000"
		},
		img: {
			"height": "auto",
			"width": imgWidth
		},
		"iframe":{
			"height": imgHeight,
			"width": imgWidth
		},
		"#content": {
			"align": "center",
			"font-size": screenFont,
			"min-width": "320px",
			"padding": "10px 20px"
		},
		"p": {
			"line-height": "20px",
			"max-width": screenMax,
			"text-indent": "20px"
		},
		"dl": {
			"padding": screenPad,
			"dt": {
				"font-weight": "bold",
				"margin-bottom": "5px"
			},
			"dd": {
				"line-height": "20px",
				"margin-bottom": "10px",
				"max-width": screenMax,
				"text-indent": "20px"
			}
		},
		"figure": {
			"font-style": "italic",
			"margin": "0"
		},
		"pre": {
			"display": "inline-block",
			"margin": screenMargin,
			"height": "auto",
			"font-size": screenFont,
			"text-indent": "0"
		},
		"#tweets": {
			"background": "#565656",
			"float": "left",
			"margin": "10px",
			"max-width": "720px",
			"padding": screenPad,
			"width": "50%"
		},
		"#commonWords": {
			"border": "2px solid #FFFFFF",
			"display": "inline-block",
			"float" : "left",
			"margin": "10px",
			"text-indent": "0"
		},
		"#clear": {
			"clear": "both"
		}
	};
	return json;
}

// selectors are the css selectors: body, #nav, #content, div
// document refers to the document object model from javascript
// json referes to the json object above
//                  selector, document, json
function styleApply(selector, parent, object){
	var element, signal;
	
	// getting the initial selectors through dom manipulation
	// element refer to the elements on the page
	// check the first character in selector
	// element returns an object of all the elements there are with matching properties in the document
	// for the case of the id it returns just that particular element, so it should be enclosed in array brackets to match everything else
	
	switch(selector[0]){
		case ".":
			element= parent.getElementsByClassName(selector.substring(1));
			break;
		case "#":
			element= [parent.getElementById(selector.substring(1))];
			break;
		default:
			element= parent.getElementsByTagName(selector);
		 	break;
	}
	
	//element[0] will not return a null if the element if found on the page, this prevents javascript from setting attributes to nodes that do not exist in the document
	
	//Refresher course: 
	//object[selector] is json["body"]
	//property is property inside each respective selector, for the first case, it is #nav
	//value is the value associated with the nav, which is the object li
	//in the case of object, line 164 is activated to recursively loop through this function again and to traverse through child elements in the current selector
	
	if(element[0] !== null){
		for(var i=0; i<element.length; i++){ //runs through the list of elements belonging to the respective selector
			for (var property in object[selector]){
			
				var value= object[selector][property];

				if(typeof(value) === "string"){
					element[i].style.setProperty(property, value);
				}else{
					styleApply(property, element[i], object[selector]);  
				}
			}
		}
	} 
}

//Design for screen width
//have an option for vertical orientation for both ipad and iphone
(function(){
	//grabs current width of the page upon load, this is a flag to set up a conditional statement later
	var width= document.width;
	
	function resize(){
		
		//all apple devices have a retina display. In order to fit the screen appropriately everything should be doubled in size compared to desktop
		//some exceptions are made for appeal
		// for the first example, although the device has a portrait width of 320px it really has 640 px because of the retina display
		//all iphone in portrait have a width of 320px
		if(window.matchMedia('only screen and (max-device-width: 320px) and (orientation: portrait)').matches){
			newWidth= 320; //newWidth is a foo variable to compare with the variable "width"
			imgHeight= "400px"; //all these variables are made for the json object to reference
			imgWidth= "600px";
			screenFont= "26";
			screenMargin= "10px";
			screenMax= "580";
			screenPad= "0px 20px";
		}
		//iphone 4 and lower in landscape with 480px width in landscape mode
		else if(window.matchMedia('only screen and (max-device-width: 480px) and (orientation: landscape)').matches){
			newWidth= 480;
			imgHeight= "500px";
			imgWidth= "880px";
			screenFont= "26";
			screenMargin= "20px";
			screenMax= "900";
			screenPad= "20px 30px";
		}
		//iphone 5 in landscape with 568px
		else if(window.matchMedia('only screen and (max-device-width: 568px) and (orientation: landscape)').matches){
			newWidth= 568;
			imgHeight= "560px";
			imgWidth= "1000px";
			screenFont= "26";
			screenMargin= "20px";
			screenMax= "1000";
			screenPad= "20px 30px";
		}
		//ipad in portrait with 768px, unfortunately, I do not own an Ipad so I cannot test this, It should work since none of the values are larger than the max device width. I try to play it safe by not utilizing the max device width
		else if(window.matchMedia('only screen and (max-device-width: 768px) and (orientation: portrait)').matches){
			newWidth= 768;
			imgHeight= "620px";
			imgWidth= "1000px";
			screenFont= "28";
			screenMargin= "40px";
			screenMax= "1440px";
			screenPad= "20px 30px";
		}
		//ipad in landscape with 1024px in width, 
		else if(window.matchMedia('only screen and (max-device-width: 1024px)').matches){
			newWidth= 1024;
			imgHeight= "620px";
			imgWidth= "1000px";
			screenFont= "28";
			screenMargin= "20px 40px";
			screenMax= "1440px";
			screenPad= "30px";
		}
		
		//none of the above conditions will work, now it is time to design for desktop widths
		//667px width, my favorite way to browse the web
		else if(window.matchMedia('only screen and (max-width: 667px)').matches){
			newWidth= 667;
			imgHeight= "320px";
			imgWidth= "500px";
			screenFont= "16";
			screenMargin= "5px 10px";
			screenMax= "690px";
			screenPad= "5px";
		}
		//accounting for 1024px screens
		//from this point all images are fixed to 500px to prevent overstretching the images
		else if(window.matchMedia('only screen and (max-width: 1024px)').matches){
			newWidth= 1024;
			imgHeight= "315px";
			imgWidth= "500px";
			screenFont= "16";
			screenMargin= "10px 20px";
			screenMax= "690px";
			screenPad= "10px";
		}
		//accounting for 1280px and 1366px for laptop users
		else if(window.matchMedia('only screen and (max-width: 1280px)').matches || window.matchMedia('only screen and (max-width: 1366px)').matches){
			newWidth= 1280;
			imgHeight= "320px";
			imgWidth= "500px";
			screenFont= "16";
			screenMargin= "10px 20px";
			screenMax= "720px";
			screenPad= "20px";
		}
		//for all screens with width 1366px and higher, page will default to same as previous  
		else{
			newWidth= 1367;
			imgHeight= "320px";
			imgWidth= "500px";
			screenFont= "16";
			screenMargin= "10px 20px";
			screenMax= "720px";
			screenPad= "20px";
		}
		
		//this if loop will only run if newWidth does not match initial screen width, since the content inside calls a function, it would be quite expensive to keep calling the function to reload the css for the page everytime the dimension changed. This way, it will only change once if it matches a certain resolution from the choices above
		if(width !== newWidth){
			width = newWidth; //makes sure this loop will not run again until next screen change
			var json = css(); //declaring local variable making changes to previously undefined variables 
			
			//make styling changes to page as necessary using styleApply, function starts on line 127
			for (var selector in json){
				styleApply(selector, document, json);
			}
		}
	}
	
	//this is the only funciton that will be called continuously to respond to changes in the screen
	window.addEventListener('resize', resize, false);
	resize();
}());