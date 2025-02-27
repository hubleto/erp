// import HubletoMain from "@hubleto/src/Main";
import OrdersTableOrders from "./Components/TableOrders";

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('OrdersTableOrders', OrdersTableOrders);
  }
}
