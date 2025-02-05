import HubletoMain from "../../../App";
import CoreCustomersTablePersons from "./Components/TablePersons"
import CoreCustomersTableCompanies from "./Components/TableCompanies"
import CoreCustomersTableActivities from "./Components/TableActivities"
import CoreCustomersTableAddresses from "./Components/TableAddresses"
import CoreCustomersTableContacts from "./Components/TableContacts"

export default class Loader {
  uid: string = 'customers';
  constructor(app: HubletoMain) {
  console.log('customers constructor');
    app.registerReactComponent('CoreCustomersTablePersons', CoreCustomersTablePersons);
    app.registerReactComponent('CoreCustomersTableCompanies', CoreCustomersTableCompanies);
    app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
    app.registerReactComponent('CoreCustomersTableAddresses', CoreCustomersTableAddresses);
    app.registerReactComponent('CoreCustomersTableContacts', CoreCustomersTableContacts);
  }
}
