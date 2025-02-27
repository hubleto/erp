// import HubletoMain from "@hubleto/src/Main";
import SalesTableDeals from "./Components/TableDeals"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('SalesTableDeals', SalesTableDeals);
  }
}
