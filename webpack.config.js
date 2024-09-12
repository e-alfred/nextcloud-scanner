const path = require('path');
const webpack = require('webpack');

module.exports = {
  mode: 'production',
  entry: {
    main: [
      path.join(__dirname, 'src', 'main.js'),
    ],
  },
  output: {
    path: path.resolve(__dirname, './js'),
    publicPath: '/js/',
    filename: '[name].js',
    chunkFilename: 'chunks/[name]-[hash].js',
  },
  plugins: [
    // fix "process is not defined" error:
    new webpack.ProvidePlugin({
      process: 'process/browser.js',
    }),
  ]
}