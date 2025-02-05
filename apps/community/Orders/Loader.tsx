import HubletoMain from "../../../App";
import OrdersTableOrders from "./Components/TableOrders";

export default class Loader {
  uid: string = 'orders';
  constructor(app: HubletoMain) {
    app.registerReactComponent('OrdersTableOrders', OrdersTableOrders);
  }
}
