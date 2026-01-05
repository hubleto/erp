import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import BillingTableBillingAccountService from "./Components/TableBillingAccountServices"

class BillingApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('BillingTableBillingAccountService', BillingTableBillingAccountService);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Billing', new BillingApp());

