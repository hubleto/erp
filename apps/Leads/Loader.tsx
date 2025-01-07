import CeremonyCrmApp from "../../../App";
import SalesTableLeads from "./Components/TableLeads"

export default class Loader {
  uid: string = 'leads';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('SalesTableLeads', SalesTableLeads);
  }
}
