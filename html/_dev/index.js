
import('./configfile.mjs').then(async (cfg) => {
	
	const methodName = process.argv.length > 2 ? process.argv[2] : 'default';
	
	if ('function' !== typeof(cfg[methodName])) {
		throw new Error(`function '${methodName}' missing`);
	}
	
	await cfg[methodName]();
});
