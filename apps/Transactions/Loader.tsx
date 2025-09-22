import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableTransactions from "@hubleto/apps/Transactions/Components/TableTransactions";
import FormTransaction from "@hubleto/apps/Transactions/Components/FormTransaction";

class TransactionsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('TableTransactions', TableTransactions);
    globalThis.main.registerReactComponent('FormTransaction', FormTransaction);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Transactions', new TransactionsApp());
