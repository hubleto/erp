import CeremonyCrmApp from "../../../App";
import SalesTableDeals from "./Components/TableDeals"

export default class Loader {
  uid: string = 'deals';
  constructor(app: CeremonyCrmApp) {
    app.registerReactComponent('SalesTableDeals', SalesTableDeals);
  }
}
