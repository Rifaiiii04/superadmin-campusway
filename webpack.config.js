const path = require('path');

module.exports = {
  entry: './resources/js/app.jsx',
  output: {
    path: path.resolve(__dirname, 'public/build'),
    filename: 'app.js',
    library: 'App',
    libraryTarget: 'umd',
    globalObject: 'window'
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-react']
          }
        }
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  externals: {
    'react': 'React',
    'react-dom': 'ReactDOM'
  },
  resolve: {
    extensions: ['.js', '.jsx']
  }
};
