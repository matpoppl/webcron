
module.exports = {
	plugins: [
		require('autoprefixer'),
		require('./plugins/postcss-csstree-validator.js'),
	],
};
