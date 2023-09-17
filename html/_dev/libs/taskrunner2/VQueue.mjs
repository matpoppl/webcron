import { createVFile } from './VFile.mjs';

/**
 * @param {function[]} transforms
 * @param {VFile} inputFile
 * @returns {Promise}
 */
async function runTransforms(transforms, inputFile)
{
	const steps = transforms.map(transform => (file) => new Promise((resolve, reject) => {

		let p = transform(file, (err, ret) => {
			if (err) return reject(err, ret);
			resolve(err, ret);
		});
		
		if (! (p instanceof Promise)) return;
		
		p.then((ret) => {
			resolve(null, ret);
		});
	}));
	
	try {
		for (const step of steps) {
			await step(inputFile);
		}
	} catch (err) {
		console.error(err);
	}
	
	return inputFile;
}

export class VQueue
{
	constructor(globRets)
	{
		this.globRets = globRets;
		this.transforms = [];
	}
	
	pipe(step)
	{
		this.transforms.push(step);
		return this;
	}
	
	_createPromise()
	{
		return Promise.allSettled(this.globRets.map(globRet => Promise.allSettled(globRet.files.map(file => runTransforms(this.transforms, createVFile(file, globRet.base))))));
		
		//return Promise.allSettled(this.globRet.files.map(file => runTransforms(this.transforms, createVFile(file, this.globRet.base))));
		//return Promise.all(this.globRet.files.map(file => runTransforms(this.transforms, createVFile(file, this.globRet.base))));
	}
}
