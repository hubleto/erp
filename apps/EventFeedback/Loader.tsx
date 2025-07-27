// How to add any React Component to be usable in Twig templates as '<app-*></app-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Hubleto framework
// globalThis.main.registerReactComponent('EventFeedbackTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <app-eventfeedback-table-my-model string:some-property="some-value"></app-eventfeedback-table-my-model>