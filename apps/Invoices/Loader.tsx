import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableProfiles from "./Components/TableProfiles"
import TableInvoices from "./Components/TableInvoices"
import TableItems from "./Components/TableItems"
import TablePayments from "./Components/TablePayments"

class InvoicesApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('InvoicesTableProfiles', TableProfiles);
    globalThis.main.registerReactComponent('InvoicesTableInvoices', TableInvoices);
    globalThis.main.registerReactComponent('InvoicesTableItems', TableItems);
    globalThis.main.registerReactComponent('InvoicesTablePayments', TablePayments);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Invoices', new InvoicesApp());
