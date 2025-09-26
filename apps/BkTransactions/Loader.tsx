import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableTransactions from "@hubleto/apps/BkTransactions/Components/TableTransactions";
import FormTransaction from "@hubleto/apps/BkTransactions/Components/FormTransaction";

class BkTransactionsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('TableTransactions', TableTransactions);
    globalThis.main.registerReactComponent('FormTransaction', FormTransaction);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/BkTransactions', new BkTransactionsApp());
