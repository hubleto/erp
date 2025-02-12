import TableSources from "./Components/TableSources"
import HubletoMain from "../../../App";

export default class Loader {
  uid: string = 'customers';
  constructor(app: HubletoMain) {
    app.registerReactComponent('TableSources', TableSources);
  }
}
