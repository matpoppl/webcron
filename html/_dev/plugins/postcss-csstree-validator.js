const { validate } = require('csstree-validator');

const logger = require('./logger.js');

module.exports = (opts = {}) => {
	return {
		postcssPlugin: 'MY_CSSTREE_VALIDATOR',
		
		Once (root) {

			validate(root.source.input.css).forEach(err => {
				const msg = `CSSTREE: ${root.source.input.file}:${err.line}:${err.column}\n${err.message}` + (err.details ? "\n" + err.details : '');
				logger.error(msg);
			});
		}
	};
};

module.exports.postcss = true;
