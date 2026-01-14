import App from '@hubleto/react-ui/core/App'
import TableContacts from "./Components/TableContacts"

class ContactsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ContactsTableContacts', TableContacts);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Contacts', new ContactsApp());
