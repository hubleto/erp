import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableAccounts from "./Components/TableAccounts";
import FormAccount from './Components/FormAccount';
import TableEntries from "@hubleto/apps/Accounting/Components/TableEntries";
import FormTransaction from "@hubleto/apps/Accounting/Components/FormTransaction";
import TableTransactions from "@hubleto/apps/Accounting/Components/TableTransactions";

class Accounting extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('FormAccount', FormAccount);
    globalThis.main.registerReactComponent('TableAccounts', TableAccounts);
    globalThis.main.registerReactComponent('TableEntries', TableEntries);

    globalThis.main.registerReactComponent('TableTransactions', TableTransactions);
    globalThis.main.registerReactComponent('FormTransaction', FormTransaction);


  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Accounting', new Accounting());
