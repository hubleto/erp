import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import InvoicesTableInvoices from "./Components/TableInvoices"
import InvoicesTableInvoiceItems from "./Components/TableInvoiceItems"

class InvoicesApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('InvoicesTableInvoices', InvoicesTableInvoices);
    globalThis.main.registerReactComponent('InvoicesTableInvoiceItems', InvoicesTableInvoiceItems);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Invoices', new InvoicesApp());
