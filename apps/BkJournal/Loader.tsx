import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableEntries from "./Components/TableEntries";

class BkJournalApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('TableEntries', TableEntries);
    // globalThis.main.registerReactComponent('InvoicesTableInvoiceItems', InvoicesTableInvoiceItems);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/BkJournal', new BkJournalApp());
