import React from 'react';
import App from '@hubleto/react-ui/core/App'
import TableCategories from "./Components/FC/TableCategories";
import TableGroups from "./Components/FC/TableGroups";
import TableProducts from "./Components/FC/TableProducts";

class ProductsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ProductsTableCategories', TableCategories);
    globalThis.hubleto.registerReactComponent('ProductsTableGroups', TableGroups);
    globalThis.hubleto.registerReactComponent('ProductsTableProducts', TableProducts);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Products', new ProductsApp());
