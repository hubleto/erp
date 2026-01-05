import React, { Component } from 'react'
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableWarehouses from "./Components/TableWarehouses"
import TableInventory from "./Components/TableInventory"
import TableLocations from "./Components/TableLocations"
import TableTransactions from "./Components/TableTransactions"
import TableTransactionItems from "./Components/TableTransactionItems"
import FormTransaction from "./Components/FormTransaction"

class WarehousesApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('WarehousesTableWarehouses', TableWarehouses);
    globalThis.hubleto.registerReactComponent('WarehousesTableInventory', TableInventory);
    globalThis.hubleto.registerReactComponent('WarehousesTableLocations', TableLocations);
    globalThis.hubleto.registerReactComponent('WarehousesTableTransactions', TableTransactions);
    globalThis.hubleto.registerReactComponent('WarehousesTableTransactionItems', TableTransactionItems);
    globalThis.hubleto.registerReactComponent('WarehousesFormTransaction', FormTransaction);

    // custom tabs
    globalThis.hubleto.getApp('Hubleto/App/Community/Products').addCustomFormTab({
      uid: 'inventory',
      title: 'Inventory',
      onRender: (form: any) => {
        return <TableInventory
          uid={form.props.uid + "_table_product_inventory"}
          tag="table_product_inventory"
          parentForm={form}
          view="productOverview"
          idProduct={form.state.record.id}
        />;
      },
    });

    globalThis.hubleto.getApp('Hubleto/App/Community/Products').addCustomFormTab({
      uid: 'transactions',
      title: 'Transactions',
      onRender: (form: any) => {
        return <TableTransactions
          uid={form.props.uid + "_table_product_transactions"}
          tag="table_product_transactions"
          parentForm={form}
          view="productOverview"
          idProduct={form.state.record.id}
        />;
      },
    });
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Suppliers', new WarehousesApp());
