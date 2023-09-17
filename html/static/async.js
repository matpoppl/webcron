
/**
 * @param {EventTarget} target
 * @param {string} name
 * @returns {Promise} name
 */
export function on(target, name)
{
	return new Promise((resolve) => {
		target.addEventListener(name, evt => {
			if (false === resolve.call(target, evt)) {
				evt.preventDefault();
			}
		});
	});
}

export class HttpRequest
{
	/**
	 * @param {URL|string} uri
	 * @param {object} options
	 */
	constructor(uri, options)
	{
		this.method = 'GET';
		this.headers = {};
		this.body = null;
		this.uri = (uri instanceof URL) ? uri : new URL(uri);

		if ('method' in options) {
			this.method = options.method;
		}
		if ('body' in options) {
			this.body = options.body;
		}
		if ('headers' in options) {
			this.setHeaders(options.headers);
		}
	}
	
	/**
	 * @param {object} headers
	 */
	setHeaders(headers)
	{
		Object.keys(headers).forEach(headerName => {
			this.setHeader(headerName, headers[headerName]);
		});
		return this;
	}
	
	/**
	 * @param {string} name
	 * @param {string|Array} value
	 */
	setHeader(name, value)
	{
		const key = name.toLowerCase();
		if (value instanceof Array) {
			this.headers[key] = value;
		} else {
			this.headers[key] = [value];
		}
		return this;
	}
	
	/**
	 * @param {string} name
	 * @returns {string}
	 */
	getHeaderLine(name)
	{
		const key = name.toLowerCase();
		return (key in this.headers) ? this.headers[key].join(', ') : '';
	}
}

/**
 * @param {HttpRequest} request
 * @param {Object|NULL} options
 * @returns {Promise}
 */
export function fetch(request, options)
{
	const config = {
		async: true,
		events: [],
		...(options || {})
	};
	
	return new Promise((resolve, reject) => {
		const xhr = new XMLHttpRequest();
	
		xhr.open(request.method, request.uri.toString(), config.async, request.uri.username, request.uri.password);
		
		on(xhr, 'load').then(evt => resolve(xhr.response, xhr.statusText, xhr, evt));
		on(xhr, 'error').then(evt => reject(xhr, xhr.statusText, evt));
		on(xhr, 'abort').then(evt => reject(xhr, xhr.statusText, evt));
		
		config.events.forEach((name, cb) => {
			on(xhr, name).then(cb);
		});
		
		Object.keys(request.headers).forEach(headerName => {
			xhr.setRequestHeader(headerName, request.getHeaderLine(headerName));
		});
		
		xhr.send(request.body);
	});
}
