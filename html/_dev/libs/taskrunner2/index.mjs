import { VQueue } from './VQueue.mjs';
import { glob } from './../glob/glob.mjs';
import { mkdirSync, writeFileSync, statSync, watchFile } from 'fs';
import { dirname } from 'path';

async function handleTask(fn)
{
	function run(fn)
	{
		return new Promise((resolve, reject) => {
			let done = false;
			
			const ret = fn((cbRet) => {
				if (done) return;
				done = true;
				resolve(cbRet);
			});
			
			if (ret instanceof VQueue) {
				return ret._createPromise().then(resolve, reject);
			}
			
			if (ret instanceof Promise) {
				return ret.then(resolve, reject);
			}
		});
	}
	//const hasName = !! fn.name;
	
	const start = performance.now();
	
	if (fn.name) {
		console.log(`[${(new Date()).toLocaleTimeString()}] Starting '${fn.name}'`);
	}
	
	await run(fn);
	
	if (fn.name) {
		console.log(`[${(new Date()).toLocaleTimeString()}] Finished '${fn.name}' after ${(performance.now() - start).toFixed(0)}ms`);
	}
}

export function src(patterns)
{
	if (! (patterns instanceof Array)) {
		patterns = [patterns];
	}
	
	const globs = patterns.map(pattern => {
		
		const matched = glob(pattern);
		// filter: _basename.ext
		matched.files = matched.files.filter(file => file.indexOf('_') !== 0 && file.indexOf('/_') < 0);
		
		return matched;
	});
	
	return new VQueue(globs);
}

function exists(pathname)
{
	try {
		statSync(pathname);
		return true;
	} catch(err) {}
	
	return false;
} 

export function dest(target)
{
	return async (file) => {

		file.dirname = target;
		const pathname = file.relative
		let dirExists = exists(dirname(pathname));

		if (! dirExists) {
			mkdirSync(dirname(pathname), { recursive: true });
		}
		
		writeFileSync(pathname, file.contents);
		
		return Promise.resolve(null, file);
	};
}

export function watch(pattern, cb)
{
	const matched = glob(pattern);
	
	matched.files.forEach(file => {
		watchFile(file, {
			interval: 200,
		}, (curr, prev) => {
			// just accessed, not modified
			if (curr.mtimeMs === prev.mtimeMs) return;
			cb();
		});
	});
}

export function series(...tasks)
{
	return async () => {
		for (const task of tasks) {
			await handleTask(task);
		}
	};
}

export function parallel(...tasks)
{
	return async () => {
		await Promise.all(tasks.map(task => handleTask(task)));
	};
}

export default { src, dest, watch, series, parallel };
