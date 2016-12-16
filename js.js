var adCounter = 0;

$(document).ready(function(){
	
	var img = new Image();
	changeAd("https://s-media-cache-ak0.pinimg.com/236x/2c/4e/0d/2c4e0d814530c9aba27bc01e41cc1340.jpg");
	
	//coloring links (red) when mouse is hovering on them
	$(document).on('mouseover','li div a',function(){
		$(this).css("color","red");
		
	});
	$(document).on('mouseout','li div a',function(){
		$(this).css("color","white");
	});
	
	//interval function that sends every 5seconds ad-url to function that draws the ad on canvas.
	setInterval(function(){
		var ads = ["https://s-media-cache-ak0.pinimg.com/236x/2c/4e/0d/2c4e0d814530c9aba27bc01e41cc1340.jpg",
					"http://s0.geograph.org.uk/geophotos/02/57/99/2579963_0228a19d.jpg",
					"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4ZCg2svjOgjul1i0jU1XP0OxRadzsv7rme9X04obNAnZ9VNGAOQ"];
		adCounter++;
		if(adCounter == 3){
				adCounter = 0;
			}
		changeAd(ads[adCounter]);
		
		
	}, 5000);
	
	//when the ad is clicked, opens the company's site on a new tab
	$('#ad').click(function(){
		var advertedSites = ["http://www.kodiakcamera.com/","http://www.kodiakcamera.com/", "https://www.microsoft.com"];
		var newWindow = window.open(advertedSites[adCounter], '_blank');
		newWindow.focus();
	});
	
	//function that draws the ad on the canvas
	function changeAd(ad){
		
		if(document.getElementById('ad') != null){
			var canvas = document.getElementById('ad');
			var ctx = canvas.getContext('2d');
			
			img.onload = function(){
				ctx.drawImage(img,0,0, 180,300);
			};
			img.src = ad;
			
			
		}
	}
	
});