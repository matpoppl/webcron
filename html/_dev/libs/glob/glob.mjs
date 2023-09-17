import { opendirSync, statSync } from 'fs';

/**
 * @param {String} pattern
 * @returns {RegExp}
 */
function createPattern(pattern)
{
	//normalize
	pattern = pattern.replaceAll('\\', '/');
	
	// simple
	pattern = pattern.replace('.', '\\.');
	pattern = pattern.replace('?', '.');
	
	const start = pattern.indexOf('{');
	if (start > -1) {
		pattern = parseBraces(pattern, start);
	}

	// substitute
	pattern = pattern.replace('**/', '###');

	pattern = pattern.replace('*', '[^/]+')

	// fix substitution
	pattern = pattern.replace('###', '.*');

	return new RegExp(pattern + '$', 'g');
}

/**
 * @param {String} pattern
 * @param {Number} start
 * @returns {String}
 */
function parseBraces(pattern, start)
{
	const end = pattern.indexOf('}', start);
	
	if (start > end) {
		throw new Error('Missing matching brace');
	}

	const oldPart = pattern.substring(start, end + 1);
	let newPart = oldPart.substring(1, oldPart.length - 1);
	newPart = newPart.replaceAll(',', '|');

	return pattern.replace(oldPart, `(${newPart})`);
}

/**
 * @param {String} pattern
 * @returns {Function}
 */
export function createFilter(pattern)
{
	const re = createPattern(pattern);
	return (str) => {
		re.lastIndex = -1;
		return re.test(str);
	};
}

function* deepls(path)
{
	// normalize slashes
	path = path.replace('\\', '/').replace(/\/+$/g, '') + '/';
	
	const dir = opendirSync(path);
	let dirent;
	while (dirent = dir.readSync()) {
		let child = path + dirent.name;
		if (dirent.isDirectory()) {
			yield* deepls(child);
		} else {
			yield child;
		}
	}
	dir.closeSync();
}

export function globDirname(pattern)
{
	// normalize slashes
	pattern = pattern.replace('\\', '/').replace(/\/+$/g, '') + '/';
	
	let prev = '', curr;
	let pos = 0;
	
	while (-1 < (pos = pattern.indexOf('/', pos))) {

		curr = pattern.substring(0, pos);
	
		try {
			let s = statSync(curr);
			
			if (! s.isDirectory()) {
				break;
			}
			
			pos++;
			prev = curr;
			
		} catch(err) {
			break;
		}
	}

	return prev;
}

/**
 * @param {String} pattern
 * @returns {Object} { base: String, files: String[] }
 */
export function glob(pattern)
{
	const filter = createFilter(pattern);
	const base = globDirname(pattern);
	const files = [];
	
    for (const n of deepls(base)) {
		if (filter(n)) {
			files.push(n);
		}
	}

	return { base: base, files: files };
}

export default { glob, globDirname, createFilter };
