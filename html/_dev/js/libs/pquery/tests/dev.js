import $ from '/_dev/js/libs/pquery/src/pQuery.js';

$('#btn1, #btn2').on('click', evt => {
	
	console.log('pQuery', {
		target: evt.target,
		currentTarget: evt.currentTarget,
		delegateTarget: evt.delegateTarget,
	});
	
});

jQuery('#btn1, #btn2').on('click', evt => {
	
	console.log('jQuery', {
		target: evt.target,
		currentTarget: evt.currentTarget,
		delegateTarget: evt.delegateTarget,
	});
	
});

$('body').on('foo', '[class]', evt => {
	
	console.log('pQuery', {
		target: evt.target,
		currentTarget: evt.currentTarget,
		delegateTarget: evt.delegateTarget,
	});
	
});

$('#btn4').on('click', () => {
	
	console.log('btn4');
	
	$('#btn3').trigger('foo');
	
});

jQuery('body').on('foo', '[class]', evt => {
	
	console.log('jQuery', {
		target: evt.target,
		currentTarget: evt.currentTarget,
		delegateTarget: evt.delegateTarget,
	});
	
});