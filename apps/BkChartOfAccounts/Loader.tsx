import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'

class BkChartOfAccountsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    // globalThis.main.registerReactComponent('InvoicesTableInvoices', InvoicesTableInvoices);
    // globalThis.main.registerReactComponent('InvoicesTableInvoiceItems', InvoicesTableInvoiceItems);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/BkChartOfAccounts', new BkChartOfAccountsApp());
