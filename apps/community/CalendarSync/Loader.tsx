import TableCalendarSyncSources from "./Components/TableCalendarSyncSources"
import HubletoMain from "../../../App";

export default class Loader {
  uid: string = 'customers';
  constructor(app: HubletoMain) {
    app.registerReactComponent('TableCalendarSyncSources', TableCalendarSyncSources);
  }
}
