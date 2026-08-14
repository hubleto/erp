import App from '@hubleto/react-ui/core/App'
import BillingTableBillingAccountService from "./Components/TableBillingAccountServices"

class BillingApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('BillingTableBillingAccountService', BillingTableBillingAccountService);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Billing', new BillingApp());

