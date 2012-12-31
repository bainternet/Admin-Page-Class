/**
 * 	Iphone styled checkboxes
 * 	@author Ohad Raz http://en.bainternet.info
 * 	@version 0.1
 * 	@copyright 2012 Ohad Raz
 */
;(function ( $, window, document, undefined ) {
	var options = {
		style: "iphone", 		//iphone,firerift,
		offClass: 'no-fancy',	//class to disable elements with this class will not be fancy
	};
	var methods = {
		init : function( settings ) { 
			if(options) {
				$.extend(options,settings);
			}
			
			return this.each(function(){
				var $elem = $(this);
				methods.loadStyledCehckboxes($elem,options);
			});
		},
		
		loadStyledCehckboxes: function($el, options){
			var $ele = $el;
			if ($ele.hasClass(options.offClass) || $ele.data('FancyCheckbox'))
				return;
			$ele.trigger('beforeLoad', $ele);
			var thisID		= $ele.attr('id');
			var setClass = '';
			switch(options.style) {
				case "iphone":
					setClass = "iphone-style";
				break;
				case "firerift":
					setClass = "firerift-style";
				break;
			}
			if($ele[0].checked == true){
				setClass = setClass + ' on';
			}else{
				setClass = setClass + ' off';
			}
			
			$ele.addClass('hidden');
			$ele.data('FancyCheckbox',true);
			
			var d = $('<div>')
				.addClass(setClass)
				.attr('rel',thisID)
				.html('&nbsp');
			switch(options.style) {
				case "iphone":
					d.on('click',function(){methods.OnClickIphone($ele);});
				break;
				case "firerift":
					d.on('click',function(){methods.OnClickfirerift($ele);});
				break;
			}	
			$ele.after(d);
			$ele.trigger('afterLoad', $ele);
        },
		
		OnClickIphone: function($el) {
			$el_Styled = $el.next();
        	$el.trigger('beforeChangeIphone', $el);
			if($el[0].checked) {
				methods.toggleOnOff($el,$el_Styled,false,true);
			} else {
				methods.toggleOnOff($el,$el_Styled,true,true);
			}
			$el.trigger('afterChangeIphone', $el);
        },
		
		OnClickfirerift: function($el) {
            $el_Styled = $el.next();
            $el.trigger('beforeChangeFirerift', $el);;
			if($el[0].checked) {
				methods.toggleOnOff($el,$el_Styled,false);
			} else {
				methods.toggleOnOff($el,$el_Styled,true);
			}
			$el.trigger('afterChangeFirerift', $el);
        },
		
		toggleOnOff: function ($el,$s,onOff,anim){
			anim = anim || false;
			if (onOff){
				if (anim)
					$s.animate({backgroundPosition: '0% 100%'});
				$el[0].checked = true;
				$s.removeClass('off').addClass('on');
			}else{
				if (anim)
					$s.animate({backgroundPosition: '100% 0%'});
				$el[0].checked = false;
				$s.removeClass('on').addClass('off');
			}
			//keep original change event
			$el.trigger("change", $el);
		},
		
		destroy: function(){
			$(this).each(function(){
				$el = $(this);
				$el.next().remove();
				$.removeData($el,'FancyCheckbox');
				$el.removeClass('hidden');
			});
		}
	};
	$.fn.FancyCheckbox =  function( method ) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
		}
	};
})( jQuery, window, document );