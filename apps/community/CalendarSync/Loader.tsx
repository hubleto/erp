// import HubletoMain from "@hubleto/src/Main";
import TableCalendarSyncSources from "./Components/TableCalendarSyncSources"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('TableCalendarSyncSources', TableCalendarSyncSources);
  }
}
