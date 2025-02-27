// import HubletoMain from "@hubleto/src/Main";
import ContactsTablePersons from "./Components/TablePersons"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('ContactsTablePersons', ContactsTablePersons);
  }
}
