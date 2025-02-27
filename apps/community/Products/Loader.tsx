// import HubletoMain from "@hubleto/src/Main";
import ProductsTableProducts from "./Components/TableProducts";

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('ProductsTableProducts', ProductsTableProducts);
  }
}
