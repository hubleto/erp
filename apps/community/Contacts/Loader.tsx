import HubletoMain from "../../../App";
import CoreContactsTablePersons from "./Components/TablePersons"

export default class Loader {
  uid: string = 'contacts';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreContactsTablePersons', CoreContactsTablePersons);
  }
}
