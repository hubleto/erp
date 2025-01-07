import HubletoCore from "../../../App";
import SalesTableDeals from "./Components/TableDeals"

export default class Loader {
  uid: string = 'deals';
  constructor(app: HubletoCore) {
    app.registerReactComponent('SalesTableDeals', SalesTableDeals);
  }
}
