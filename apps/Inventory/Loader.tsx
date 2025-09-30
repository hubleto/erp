import React from 'react';
import TableInventory from "./Components/TableInventory"
import TableTransactions from "./Components/TableTransactions"
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'

class InventoryApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('InventoryTableInventory', TableInventory);
    globalThis.main.registerReactComponent('InventoryTableTransactions', TableTransactions);

    // miscellaneous
    globalThis.main.getApp('Hubleto/App/Community/Products').addCustomFormTab({
      uid: 'inventory',
      title: <span className='italic'>Inventory</span>,
      onRender: (form: any) => {
        return <TableInventory
          tag={"table_project_order"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_product_inventory"}
          idProduct={form.state.record.id}
        />;
      },
    });
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Inventory', new InventoryApp());

