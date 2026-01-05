import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableReceipts from "./Components/TableReceipts"
import TableCashRegisters from "./Components/TableCashRegisters"

class CashdeskApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CashdeskTableReceipts', TableReceipts);
    globalThis.hubleto.registerReactComponent('CashdeskTableCashRegisters', TableCashRegisters);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Cashdesk', new CashdeskApp());
