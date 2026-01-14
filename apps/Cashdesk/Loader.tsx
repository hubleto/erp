import App from '@hubleto/react-ui/core/App'
import TableReceipts from "./Components/TableReceipts"
import TableCashRegisters from "./Components/TableCashRegisters"

class CashdeskApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CashdeskTableReceipts', TableReceipts);
    globalThis.hubleto.registerReactComponent('CashdeskTableCashRegisters', TableCashRegisters);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Cashdesk', new CashdeskApp());
