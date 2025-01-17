import HubletoMain from "../../../App";
import ShopTableProducts from "./Components/TableProducts";
import ShopTableOrders from "./Components/TableOrders";

export default class Loader {
  uid: string = 'shop';
  constructor(app: HubletoMain) {
    app.registerReactComponent('ShopTableProducts', ShopTableProducts);
    app.registerReactComponent('ShopTableOrders', ShopTableOrders);
  }
}
