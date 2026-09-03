import App from '@hubleto/react-ui/core/App'
import TableAccounts from "./Components/FC/TableAccounts"
import TableMails from "./Components/FC/TableMails"
import TableTemplates from "./Components/FC/TableTemplates"

class MailApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('MailTableAccounts', TableAccounts);
    globalThis.hubleto.registerReactComponent('MailTableMails', TableMails);
    globalThis.hubleto.registerReactComponent('MailTableTemplates', TableTemplates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Mail', new MailApp());
