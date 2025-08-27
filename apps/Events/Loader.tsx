// How to add any React Component to be usable in Twig templates as '<app-*></app-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Hubleto framework
// globalThis.main.registerReactComponent('EventsTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <app-events-table-my-model string:some-property="some-value"></app-events-table-my-model>

import TableEvents from './Components/TableEvents'
import TableTypes from './Components/TableTypes'
import TableVenues from './Components/TableVenues'
import TableAttendees from './Components/TableAttendees'
import TableEventAttendees from './Components/TableEventAttendees'
import TableEventVenues from './Components/TableEventVenues'
import TableAgendas from './Components/TableAgendas'
import TableSpeakers from './Components/TableSpeakers'
import TableEventSpeakers from './Components/TableEventSpeakers'

globalThis.main.registerReactComponent('EventsTableEvents', TableEvents);
globalThis.main.registerReactComponent('EventsTableTypes', TableTypes);
globalThis.main.registerReactComponent('EventsTableVenues', TableVenues);
globalThis.main.registerReactComponent('EventsTableAttendees', TableAttendees);
globalThis.main.registerReactComponent('EventsTableEventAttendees', TableEventAttendees);
globalThis.main.registerReactComponent('EventsTableEventVenues', TableEventVenues);
globalThis.main.registerReactComponent('EventsTableAgendas', TableAgendas);
globalThis.main.registerReactComponent('EventsTableSpeakers', TableSpeakers);
globalThis.main.registerReactComponent('EventsTableEventSpeakers', TableEventSpeakers);
