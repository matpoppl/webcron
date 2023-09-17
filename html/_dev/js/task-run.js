
($ => {

	const $results = $('#results');

	/** @var AbortController */
	var abort;

	/**
	 * @param {string} url
	 * @param {string[]} params
	 * @returns {string}
	 */
	function mergeParamsQuery(url, ...params)
	{
		const parts = url.split('?');
		const urlSearchParams = new URLSearchParams(parts.length > 1 ? parts[1] : '');
		const searchParams = params.filter(param => param.query).map(param => new URLSearchParams(param.query));
		for (const searchParam of searchParams) {
			for (const key of searchParam.keys()) {
				urlSearchParams.set(key, searchParam.get(key));
			}
		}
		return parts[0] + '?' + urlSearchParams.toString();
	}

	/**
	 * @param {string} url
	 * @param {object} params
	 */
	function call(url, params, n)
	{
		console.log('call', url, params);

		if (abort.signal.aborted) {
			return;
		}
		
		url = mergeParamsQuery(url, params, 'XDEBUG_SESSION=1');
		
		const $item = $('<li class="results__item" />').prependTo($results);
		const ts0 = performance.now();
		
		$(`<dl><dt>[${n}]${url}</dt><dd>${JSON.stringify(params)}</dd></dl>`).appendTo($item);
		
		const fetchConfig = {
			method: 'post',
			signal: abort.signal,
		};
		
		if (params.body) {
			fetchConfig.headers = {
				'Content-Type': 'application/json; charset=UTF-8',
			};
			fetchConfig.body = params.body;
		}
		
		fetch(url, fetchConfig).then(res => res.json()).then(json => {
			console.log('json', json);
			
			const ts1 = performance.now() - ts0;
			
			let html = `<div><b>${json.status} (${ts1})</b><pre>${JSON.stringify(json.params, null, "\n\t")}</pre></div>`;
			
			switch (json.status) {
				case 'error-continue':
				case 'continue':
					call(url, json.params, n + 1);
					break;
				case 'error':
				case 'done':
					break;
			}
			
			$(html).appendTo($item);
		}).catch(err => {
			let html = `<div><b>ERROR</b><pre>${err}</pre></div>`;
			$(html).appendTo($item);
		});
	}

	$('#btn-start').on('click', () => {
		console.log('start');
		
		abort = new AbortController();
		
		call(location.pathname, {body:null,query:null}, 0);
	});
	
	$('#btn-stop').on('click', () => {
		console.log('stop');
		abort.abort();
	});
	
})(pQuery);
