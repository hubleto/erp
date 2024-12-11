const path = require('path');

module.exports = (env, arg) => {
  return {
    // stats: 'verbose',
    entry: {
      app: ['./src/App.tsx'],
    },
    output: {
      path: path.resolve(__dirname, 'assets/compiled'),
      filename: '[name].js',
      clean: true
    },
    optimization: {
      minimize: true,
    },
    module: {
      rules: [
        {
          test: /\.(js|mjs|jsx|ts|tsx)$/,
          exclude: /node_modules/,
          use: 'babel-loader',
        },
        {
          test: /\.(scss|css)$/,
          use: ['style-loader', 'css-loader', 'sass-loader'],
        }
      ],
    },
    resolve: {
      modules: [ path.resolve(__dirname, './node_modules') ],
      extensions: ['.js', '.jsx', '.ts', '.tsx', '.scss', '.css'],
      alias: {
        adios: path.resolve(__dirname, './lib/vendor/wai-blue/adios/src/Components'),
      },
    }
  }
};
