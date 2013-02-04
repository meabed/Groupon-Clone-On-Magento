$j_mw(function(){	
	$j_mw("#toggleButton").click(function () {
		
			$j_mw("#toggleSection").slideToggle();
			
			if(this.innerHTML =='Hide') this.innerHTML = 'Show';
			else this.innerHTML = 'Hide';

	        return false;
		});
});