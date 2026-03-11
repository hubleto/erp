import App from '@hubleto/react-ui/core/App'
import TableAccounts from "./Components/TableAccounts"
import TableMails from "./Components/TableMails"
import TableTemplates from "./Components/TableTemplates"

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
