import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableWarehouses from "./Components/TableWarehouses"

class WarehousesApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('WarehousesTableWarehouses', TableWarehouses);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Suppliers', new WarehousesApp());
