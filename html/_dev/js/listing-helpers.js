
($ => {
	
	$('body').on('change', '.js-formaction-switch', evt => {
		evt.preventDefault();
		
		console.log(evt.target);
	});
	
	$(window).one('load', () => {
			
		$('.js-formaction-switch').each((i, ctrl) => {
			console.log(ctrl);
		});
	});
	
})(pQuery);
