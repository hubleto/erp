import HubletoMain from "../../../App";
import ProductsTableProducts from "./Components/TableProducts";

export default class Loader {
  uid: string = 'products';
  constructor(app: HubletoMain) {
    app.registerReactComponent('ProductsTableProducts', ProductsTableProducts);
  }
}
