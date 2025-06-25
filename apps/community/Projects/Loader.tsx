// How to add any React Component to be usable in Twig templates as '<app-*></app-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Adios framework
// globalThis.main.registerReactComponent('ProjectsTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <app-projects-table-my-model string:some-property="some-value"></app-projects-table-my-model>

import TableProjects from "./Components/TableProjects"
import TablePhases from './Components/TablePhases'

globalThis.main.registerReactComponent('ProjectsTableProjects', TableProjects);
globalThis.main.registerReactComponent('ProjectsTablePhases', TablePhases);
