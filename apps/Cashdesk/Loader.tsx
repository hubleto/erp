import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableReceipts from "./Components/TableReceipts"
import TableCashRegisters from "./Components/TableCashRegisters"

class CashdeskApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('CashdeskTableReceipts', TableReceipts);
    globalThis.main.registerReactComponent('CashdeskTableCashRegisters', TableCashRegisters);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Cashdesk', new CashdeskApp());
