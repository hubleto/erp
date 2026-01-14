import App from '@hubleto/react-ui/core/App'
import TableMails from "./Components/TableMails"
import TableTemplates from "./Components/TableTemplates"

class MailApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('MailTableMails', TableMails);
    globalThis.hubleto.registerReactComponent('MailTableTemplates', TableTemplates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Mail', new MailApp());
