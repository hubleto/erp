module.exports = function (api) {
  api.cache(true);

  const presets = ["@babel/preset-react", "@babel/preset-typescript"];
  const plugins = [];

  return {
    presets,
    plugins
  };
}