const path = require('path');

module.exports = (env, arg) => {
  return {
    // stats: 'verbose',
    entry: {
      hubleto: ['./index.tsx'],
    },
    output: {
      path: path.resolve(__dirname, 'assets/compiled/js'),
      filename: '[name].js',
      clean: true
    },
    // optimization: {
    //   minimize: true,
    // },
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
    optimization: {
      splitChunks: {
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            chunks: 'all'
          },
          adios: {
            test: /[\\/](ADIOS|adios)[\\/]/,
            name: 'adios',
            chunks: 'all'
          }
        }
      }
    },
    resolve: {
      modules: [ path.resolve(__dirname, './node_modules') ],
      extensions: ['.js', '.jsx', '.ts', '.tsx', '.scss', '.css'],
      alias: {
        '@hubleto': path.resolve(__dirname),
      },
    }
  }
};
