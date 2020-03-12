const path = require('path')

const config = {
	entry: {
		main:path.join(__dirname, 'src', 'main.js')
	},
	output: {
		path: path.resolve(__dirname, './js'),
		publicPath: '/js/',
		filename: './[name].js',
		chunkFilename: 'chunks/[name]-[hash].js',
	},
	module: {
		rules: [
			{
				test: /\.css$/,
				use: ['vue-style-loader', 'css-loader'],
			},
			{
				test: /\.scss$/,
				use: ['vue-style-loader', 'css-loader', 'sass-loader'],
			},
		],
	},
	resolve: {
		extensions: ['*', '.js', '.vue'],
		symlinks: false,
	},
}

module.exports = config