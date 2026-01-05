import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableMails from "./Components/TableMails"
import TableTemplates from "./Components/TableTemplates"

class MailApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('MailTableMails', TableMails);
    globalThis.hubleto.registerReactComponent('MailTableTemplates', TableTemplates);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Mail', new MailApp());
