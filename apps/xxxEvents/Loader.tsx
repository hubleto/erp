// How to add any React Component to be usable in Twig templates as '<hblreact-*></hblreact-*>' HTML tag.
// -> Replace 'MyModel' with the name of your model in the examples below

// 1. import the component
// import TableMyModel from "./Components/TableMyModel"

// 2. Register the React Component into Hubleto framework
// globalThis.hubleto.registerReactComponent('EventsTableMyModel', TableMyModel);

// 3. Use the component in any of your Twig views:
// <hblreact-events-table-my-model string:some-property="some-value"></hblreact-events-table-my-model>

import TableEvents from './Components/TableEvents'
import TableTypes from './Components/TableTypes'
import TableVenues from './Components/TableVenues'
import TableAttendees from './Components/TableAttendees'
import TableEventAttendees from './Components/TableEventAttendees'
import TableEventVenues from './Components/TableEventVenues'
import TableAgendas from './Components/TableAgendas'
import TableSpeakers from './Components/TableSpeakers'
import TableEventSpeakers from './Components/TableEventSpeakers'

globalThis.hubleto.registerReactComponent('EventsTableEvents', TableEvents);
globalThis.hubleto.registerReactComponent('EventsTableTypes', TableTypes);
globalThis.hubleto.registerReactComponent('EventsTableVenues', TableVenues);
globalThis.hubleto.registerReactComponent('EventsTableAttendees', TableAttendees);
globalThis.hubleto.registerReactComponent('EventsTableEventAttendees', TableEventAttendees);
globalThis.hubleto.registerReactComponent('EventsTableEventVenues', TableEventVenues);
globalThis.hubleto.registerReactComponent('EventsTableAgendas', TableAgendas);
globalThis.hubleto.registerReactComponent('EventsTableSpeakers', TableSpeakers);
globalThis.hubleto.registerReactComponent('EventsTableEventSpeakers', TableEventSpeakers);
