import HubletoMain from "../../../App";
import SalesTableDeals from "./Components/TableDeals";

export default class Loader {
  uid: string = 'deals';
  constructor(app: HubletoMain) {
    app.registerReactComponent('SalesTableDeals', SalesTableDeals);
  }
}
