import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableProfiles from "./Components/TableProfiles"
import TableInvoices from "./Components/TableInvoices"
import TableItems from "./Components/TableItems"
import TablePayments from "./Components/TablePayments"

class InvoicesApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('InvoicesTableProfiles', TableProfiles);
    globalThis.hubleto.registerReactComponent('InvoicesTableInvoices', TableInvoices);
    globalThis.hubleto.registerReactComponent('InvoicesTableItems', TableItems);
    globalThis.hubleto.registerReactComponent('InvoicesTablePayments', TablePayments);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Invoices', new InvoicesApp());
