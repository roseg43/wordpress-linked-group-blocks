const config = require('10up-toolkit/config/webpack.config');

const [scriptConfig, moduleConfig] = config;

module.exports = [
	scriptConfig,
	{
		...moduleConfig,
		plugins: [
			...moduleConfig.plugins.filter(
				(plugin) => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin',
			),
		],
	},
];
