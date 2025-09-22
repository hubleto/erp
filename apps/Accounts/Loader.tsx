import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableAccounts from "./Components/TableAccounts";
import FormAccount from './Components/FormAccount';

class AccountsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('FormAccount', FormAccount);
    globalThis.main.registerReactComponent('TableAccounts', TableAccounts);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Accounts', new AccountsApp());
