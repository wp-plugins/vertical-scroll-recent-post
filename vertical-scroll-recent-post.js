	

function vsrp_scroll() {
	vsrp_obj.scrollTop = vsrp_obj.scrollTop + 1;
	vsrp_scrollPos++;
	if ((vsrp_scrollPos%vsrp_heightOfElm) == 0) {
		vsrp_numScrolls--;
		if (vsrp_numScrolls == 0) {
			vsrp_obj.scrollTop = '0';
			vsrp_content();
		} else {
			if (vsrp_scrollOn == 'true') {
				vsrp_content();
			}
		}
	} else {
		setTimeout("vsrp_scroll();", 10);
	}
}

var vsrp_Num = 0;
/*
Creates amount to show + 1 for the scrolling ability to work
scrollTop is set to top position after each creation
Otherwise the scrolling cannot happen
*/
function vsrp_content() {
	var tmp_vsrp = '';

	w_vsrp = vsrp_Num - parseInt(vsrp_numberOfElm);
	if (w_vsrp < 0) {
		w_vsrp = 0;
	} else {
		w_vsrp = w_vsrp%vsrp_array.length;
	}
	
	// Show amount of vsrru
	var elementsTmp_vsrp = parseInt(vsrp_numberOfElm) + 1;
	for (i_vsrp = 0; i_vsrp < elementsTmp_vsrp; i_vsrp++) {
		
		tmp_vsrp += vsrp_array[w_vsrp%vsrp_array.length];
		w_vsrp++;
	}

	vsrp_obj.innerHTML 	= tmp_vsrp;
	
	vsrp_Num 			= w_vsrp;
	vsrp_numScrolls 	= vsrp_array.length;
	vsrp_obj.scrollTop 	= '0';
	// start scrolling
	setTimeout("vsrp_scroll();", 2000);
}

