import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import ProductsTableProducts from "./Components/TableProducts";

class ProductsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('ProductsTableProducts', ProductsTableProducts);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Products', new ProductsApp());
