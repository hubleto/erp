import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableContacts from "./Components/TableContacts"

class ContactsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ContactsTableContacts', TableContacts);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Contacts', new ContactsApp());
