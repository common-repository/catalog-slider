jQuery(document).ready(function(){

	jQuery('.slide_catalog').each(function(i){

		var nb = jQuery(this).find('.slide').length;

		jQuery(this).find('.slide').each(function(j) {

			var tr = 'perspective(3500px) translateX('+(j*20)+'px) translateY('+(j*5)+'px) rotateY(45deg)';
			jQuery(this).css({transform: tr, zIndex: j});

			if((j+1) == nb)
				jQuery(this).addClass('current');

		});

		var _this = this;
		jQuery(_this).data('slide_out', 0);
		jQuery(_this).data('nb', jQuery(_this).find('.slide').length);

		setInterval(function(){

			var slide_out = jQuery(_this).data('slide_out');
			console.log(slide_out);

			if(slide_out < (jQuery(_this).data('nb')-1))
			{
		  		jQuery(_this).find('.current').animate({left: '-=2500'}, 1000, function(){
		  			
				  	jQuery(_this).find('.current').removeClass('current');
		            slide_out++;		            
		            jQuery(_this).find('.slide-'+slide_out).addClass('current');
		            jQuery(_this).data('slide_out', slide_out);

		  		});

		  		jQuery(_this).find('.slide-'+(slide_out+1)).animate({opacity: 1}, 1000);
		  	}
		  	else
		  	{
		  		jQuery(_this).find('.slide').css("opacity", "");
		  		jQuery(_this).find('.slide:not(.current)').animate({left: '+=2500'}, 1000, function(){
		  			jQuery(_this).find('.current').removeClass('current');
			  		jQuery(_this).data('slide_out', 0);
			  		jQuery(_this).find('.slide-0').addClass('current');
		  		});
		  		
		  		
		  	}

		}, parseInt(jQuery(_this).attr('rel')));

	});
	

});