import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableContacts from "./Components/TableContacts"

class ContactsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('ContactsTableContacts', TableContacts);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Contacts', new ContactsApp());
