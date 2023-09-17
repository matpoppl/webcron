
function pQuery(selector, ctx)
{
	let data;
	
	if (selector instanceof QueryResult) {
		return selector;
	} else if ($.isString(selector)) {
		if ('<' === selector[0]) {
			const div = document.createElement('div');
			div.innerHTML = selector;
			data = div.children;
		} else {
			data = (ctx || document).querySelectorAll(selector);
		}
	} else if (selector instanceof Array) {
		data = selector;
	} else if (selector) {
		data = [selector];
	} else {
		data = [];
	}
	
	return new QueryResult(data);
}

class pQueryEvent
{
	constructor(originalEvent)
	{
		this.originalEvent = originalEvent;
		this.currentTarget = null;
	}
}

class QueryResult
{
	constructor(data)
	{
		this.length = data.length;
		$.each(data, (i, item) => {
			this[i] = item; 
		});
	}
	
	each(cb)
	{
		$.each(this, cb);
		return this;
	}
	
	map(cb)
	{
		const ret = new Array(this.length);
		$.each(this, (i, item) => {
			ret[i] = cb.call(item, i, item);
		});
		return $(ret);
	}
	
	filter(selector)
	{
		const ret = [];
		
		if ('function' === typeof(selector)) {
			
			$.each(this, (i, item) => {
				if (selector.call(item, i, item)) {
					ret.push(item);
				}
			});
			
			return $(ret);
		}
		
		$.each(this, (i, item) => {
			$.each(item.parentNode.querySelectorAll(':scope > ' + selector), (j, child) => {
				if (child === item) {
					ret.push(item);
				}
			});
		});
		
		return $(ret);
	}
	
	find(selector)
	{
		const ret = [];
		
		$.each(this, (i, parent) => {
			$.each(parent.querySelectorAll(selector), (j, child) => {
				ret.push(child);
			});
		});
		
		return $( ret );
	}
	
	first()
	{
		return $( $.onFirst(this, (item) => item) );
	}
	
	empty()
	{
		return this.each((i, parent) => {
			while (parent.firstChild) {
				parent.removeChild(parent.firstChild);
			}
		});
	}
	
	hasClass(className)
	{
		return this.each((i, elem) => {
			elem.classList.contains(className);
		});
	}
	
	addClass(className)
	{
		return this.each((i, elem) => {
			elem.classList.add(className);
		});
	}
	
	removeClass(className)
	{
		return this.each((i, elem) => {
			elem.classList.remove(className);
		});
	}
	
	toggleClass(className)
	{
		return this.each((i, elem) => {
			elem.classList.toggle(className);
		});
	}
	
	attr(name, val)
	{
		if ('object' === typeof(name)) {
			for (const x in name) {
				this.attr(x, name[x]);
			}
			return this;
		}
		
		if (undefined === val) {
			// get
			return $.onFirst(this, (item) => item.getAttribute(name));
		} else if (null === val) {
			return this.removeAttr(name);
		}
		
		return this.each((i, elem) => {
			elem.setAttribute(name, val);
		});
	}
	
	removeAttr(name)
	{
		return this.each((i, elem) => {
			elem.removeAttribute(name);
		});
	}
	
	on(types, selector, data, cb, _listenerOptions)
	{
		if (! _listenerOptions) {
			_listenerOptions = {};
		}
		
		if (! cb) {
			cb = data || selector;
			data = null;
		}
		
		if (! data && ! $.isString(selector)) {
			data = selector;
			selector = null;
		}
		
		if (selector) {
			_listenerOptions['passive'] = true;
		}
		
		if (! ('pQuery' in cb)) {
			cb.pQuery = {};
		}
		
		return this.each((i, target) => {
			$(types.split(' ')).each((i, type) => {

				if (! (type in cb.pQuery)) {
					cb.pQuery[type] = new WeakMap();
				}
				
				cb.pQuery[type].set(target, (originalEvent) => {
					
					var currentTarget = null;
					if (selector) {
						
						$(selector, target).each((i, candidate) => {
							let tmp = originalEvent.target;
							
							while (tmp && target !== tmp) {
								if (candidate === tmp) {
									currentTarget = candidate;
									return false;
								}
								tmp = tmp.parentNode;
							}
						});
						
						// selector not found
						if (! currentTarget) return;
					}
					
					const evt = new Proxy(new pQueryEvent(originalEvent), {
						get(evt, prop) {
							return (prop in evt) ? evt[prop] : evt.originalEvent[prop];
						},
						
						set(evt, prop, val) {
							if (prop in evt) {
								evt[prop] = val;
								return true;
							}
							evt.originalEvent[prop] = val;
							return true;
						}
					});
					
					if (selector) {
						evt.currentTarget = currentTarget;
						evt.delegateTarget = target;
					} else {
						evt.currentTarget = target;
						evt.delegateTarget = target;
					}
					
					evt.data = data;
					const args = ('detail' in evt && evt.detail instanceof Array) ? evt.detail : [evt.detail];
					if (false === cb.call(target, evt, ...args)) {
						evt.preventDefault();
					}
				});
		
				
				target.addEventListener(type, cb.pQuery[type].get(target), _listenerOptions);
			});
		});
	}
	
	one(type, cb)
	{
		return this.on(type, null, null, cb, { once: true });
	}
	
	off(types, cb)
	{
		return this.each((i, target) => {
			$(types.split(' ')).each((i, type) => {
				if ('pQuery' in cb && type in cb.pQuery && cb.pQuery[type].has(target)) {
					target.removeEventListener(type, cb.pQuery[type].get(target));
				}
			});

		});
	}
	
	trigger(type, ...params)
	{
		return this.each((i, target) => {
			const evt = new CustomEvent(type, { detail: params, bubbles: true });
			target.dispatchEvent(evt);
		});
	}
	
	toArray()
	{
		return Array.from(this);
	}
	
	append(items)
	{
		return this.each((i, parent) => {
			$(items).each((j, child) => {
				parent.appendChild(child);
			});
		});
	}
	
	appendTo(parents)
	{
		return this.each((i, child) => {
			$(parents).each((j, parent) => {
				parent.appendChild(child);
			});
		});
	}
	
	prepend(items)
	{
		return this.each((i, parent) => {
			$(items).each((j, child) => {
				if (parent.firstChild) {
					parent.insertBefore(child, parent.firstChild);
				} else {
					parent.appendChild(child);
				}
			});
		});
	}
	
	prependTo(parents)
	{
		return this.each((i, child) => {
			$(parents).each((j, parent) => {
				if (parent.firstChild) {
					parent.insertBefore(child, parent.firstChild);
				} else {
					parent.appendChild(child);
				}
			});
		});
	}
	
	val(newVal)
	{
		// getter
		if (undefined === newVal) {
			return $.onFirst(this, item => {
				if (item instanceof HTMLSelectElement) {
					return $(item.selectedOptions).map((i, opt) => opt.value);
				}
				
				return item.value;
			});
		}
		
		// setter
		return this.each((i, item) => {
			if (item instanceof HTMLSelectElement) {
				if ($.isArray(newVal)) {
					$('option', item).removeAttr('selected').filter((i, opt) => newVal.indexOf(opt.value) > -1).attr('selected', '');
					return;
				}
				
				$('option', item).removeAttr('selected').filter((i, opt) => newVal.indexOf(opt.value) > -1).attr('selected', '');
				return;
			}
		
			item.value = newVal;
		});
	}
	
	[Symbol.iterator]()
	{
		let i = 0;
		return {
			next: () => {
				return (i < this.length) ? { value: this[i++], done: false } : { value: undefined, done: true };
			}
		};
	}
}

const $ = pQuery;

Object.assign(pQuery, {
	
	onFirst(data, cb) {
		let ret;
		$.each(data, (i, item) => {
			ret = cb.call(item, item);
			return false;
		});
		return ret;
	},
	
	isString(val) {
		return 'string' === typeof(val) || val instanceof String;
	},
	
	each(data, cb) {
		for (let i = 0, z = data.length; i < z; i++) {
			if (false === cb.call(data[i], i, data[i], data)) break;
		}
	},
	
});

export default $;
