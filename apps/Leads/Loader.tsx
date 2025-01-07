import HubletoCore from "../../../App";
import SalesTableLeads from "./Components/TableLeads"

export default class Loader {
  uid: string = 'leads';
  constructor(app: HubletoCore) {
    app.registerReactComponent('SalesTableLeads', SalesTableLeads);
  }
}
