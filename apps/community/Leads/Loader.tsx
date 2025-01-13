import HubletoMain from "../../../App";
import SalesTableLeads from "./Components/TableLeads"

export default class Loader {
  uid: string = 'leads';
  constructor(app: HubletoMain) {
    app.registerReactComponent('SalesTableLeads', SalesTableLeads);
  }
}
