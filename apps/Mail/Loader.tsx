import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableMails from "./Components/TableMails"

class MailApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('MailTableMails', TableMails);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Mail', new MailApp());
