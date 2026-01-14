// How to add any React Component to be usable in Twig templates as '<hblreact-*></hblreact-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Hubleto framework
// globalThis.hubleto.registerReactComponent('DeveloperTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <hblreact-developer-table-my-model string:some-property="some-value"></hblreact-developer-table-my-model>