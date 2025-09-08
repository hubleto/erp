import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import InvoicesTableInvoices from "./Components/TableInvoices"
import InvoicesTableInvoiceItems from "./Components/TableInvoiceItems"

class ChartOfAccountsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    // globalThis.main.registerReactComponent('InvoicesTableInvoices', InvoicesTableInvoices);
    // globalThis.main.registerReactComponent('InvoicesTableInvoiceItems', InvoicesTableInvoiceItems);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/ChartOfAccounts', new ChartOfAccountsApp());
