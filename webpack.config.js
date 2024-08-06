const path = require('path');

module.exports = (env, arg) => {
  return {
    // stats: 'verbose',
    entry: {
      app: ['./src/priv/Components/index.tsx'],
    },
    output: {
      path: path.resolve(__dirname, 'app/assets/compiled/js'),
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
      alias: {
        "@adios": path.resolve(__dirname, "./vendor/wai-blue/adios/src/Components"),
        "@primereact": path.resolve(__dirname, "./vendor/wai-blue/adios/node_modules/primereact"),
        "@frappe-gantt-react": path.resolve(__dirname, "./vendor/wai-blue/adios/node_modules/frappe-gantt-react"),
      },
      extensions: ['.js', '.jsx', '.ts', '.tsx', '.scss', '.css'],
    }
  }
};
