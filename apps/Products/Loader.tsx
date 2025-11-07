import React from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableCategories from "./Components/TableCategories";
import TableGroups from "./Components/TableGroups";
import TableProducts from "./Components/TableProducts";

class ProductsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('ProductsTableCategories', TableCategories);
    globalThis.main.registerReactComponent('ProductsTableGroups', TableGroups);
    globalThis.main.registerReactComponent('ProductsTableProducts', TableProducts);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Products', new ProductsApp());
