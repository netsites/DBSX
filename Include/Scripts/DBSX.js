// Aditional JS functions ////////////////////////////////////////////////// --> 

function var_dump(obj) {
   if(typeof obj == "object") {
      return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
   } else {
      return "Type: "+typeof(obj)+"\nValue: "+obj;
   }
}

//Next routine allows to create lightbox images inside X editor using href property as "lightbox"
$(window).on('load', function () {

	//Find lightbox images using href
	$("a[href='lightbox']").each(function (){
		var src = $(this).find("img").attr("src");
		var img = $(this).find("img");
		$(this).attr('href',src);
		$(this).attr('rel','lightbox');		
		//REMOVE CREATED EVENTS AND IMPORTED FUNCTIONS BY XARA WHEN USE SHOW & HIDE LAYERS
		$(this).removeAttr('onclick');
		$(this).find("img").removeAttr('onmousemove');
	});

	//fix dinamic content
	$('.my_d_content').each(function() {
		if ($(this).css('display') == 'none') {
		$(this).delay(250).fadeIn();
		}
	});

	    //if is using XARA fix webpage forcing reload
		if (document.getElementById('xr_xrii') != null) {
			//alert('Xara object');
			var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|webOS|BlackBerry|IEMobile|Opera Mini)/i);
			if (!isMobile) {
				var rtime;
				var timeout = false;
				var delta = 100;
				$(window).resize(function() {
					rtime = new Date();
					if (timeout === false) {
						timeout = true;
						setTimeout(resizeend, delta);
					}
				});
	
				function resizeend() {
					if (new Date() - rtime < delta) {
						setTimeout(resizeend, delta);
					} else {
						timeout = false;
						this.location.reload(false);
					}               
				}
	
	
			}
		}
 
    
});

