import { readFileSync } from 'fs';
import { parse } from 'path';
import { Buffer } from 'buffer';

export class VFile
{
	/**
	 * @param {object} parts { root: '/', dir: '/home/user/dir', base: 'file.txt', name: 'file', ext: '.txt' }
	 * @param {string|NULL} base
	 * @param {Buffer|String} contents
	 */
	constructor(parts, base, contents)
	{
		/** @var {string} */
		this.base = (base || '').replace(/\/+$/g, '/');
		/** @var {string} */
		this._dir = parts.dir.replace('\\', '/');
		/** @var {string} */
		this._name = parts.name;
		/** @var {string} */
		this._ext = parts.ext;
		/** @var {Buffer|String} */
		this._contents = contents;
	}
	
	get contents()
	{
		if (undefined === this._contents) {
			this._contents = readFileSync(this.path);
		}
		
		return this._contents;
	}
	
	set contents(contents)
	{
		this._contents = contents;
	}
	
	get dirname()
	{
		return this._dir;
	}
	
	set dirname(dir)
	{
		this._dir = dir;
	}
	
	get basename()
	{
		return this._name + this._ext;
	}
	
	set basename(basename)
	{
		const tmp = parse(basename);
		this._name = tmp.name;
		this._ext = tmp.ext;
	}
	
	get stem()
	{
		return this._name;
	}
	
	set stem(stem)
	{
		this._name = stem;
	}
	
	get extname()
	{
		return this._ext;
	}
	
	set extname(ext)
	{
		this._ext = '.' + ext.replace(/^\.+/g, '');
	}
	
	get path()
	{
		return this.dirname + '/' + this.basename;
	}
	
	get relative()
	{
		return this.base.length > 0 ? this.path.substr(this.base.length) : this.path;
	}
	
	isNull()
	{
		return null === this.contents;
	}
	
	isStream()
	{
		return false;
	}
	
	isBuffer()
	{
		return Buffer.isBuffer( this.contents );
	}
	
	toString()
	{
		return this.path;
	}
}

export function createVFile(pathname, base, contents)
{
	const fparts = parse(pathname);
	return new VFile(fparts, base, contents);
}
