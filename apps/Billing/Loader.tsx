import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import BillingTableBillingAccountService from "./Components/TableBillingAccountServices"

class BillingApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('BillingTableBillingAccountService', BillingTableBillingAccountService);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Billing', new BillingApp());

