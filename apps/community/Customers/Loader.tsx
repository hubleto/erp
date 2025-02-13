import HubletoMain from "../../../App";
import CoreCustomersTablePersons from "./Components/TablePersons"
import CoreCustomersTableCustomers from "./Components/TableCustomers"
import CoreCustomersTableActivities from "./Components/TableActivities"
import CoreCustomersTableAddresses from "./Components/TableAddresses"
import CoreCustomersTableContacts from "./Components/TableContacts"

export default class Loader {
  uid: string = 'customers';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreCustomersTablePersons', CoreCustomersTablePersons);
    app.registerReactComponent('CoreCustomersTableCustomers', CoreCustomersTableCustomers);
    app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
    app.registerReactComponent('CoreCustomersTableAddresses', CoreCustomersTableAddresses);
    app.registerReactComponent('CoreCustomersTableContacts', CoreCustomersTableContacts);
  }
}
