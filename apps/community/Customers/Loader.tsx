import HubletoMain from "../../../App";
import CoreCustomersTableCustomers from "./Components/TableCustomers"
import CoreCustomersTableActivities from "./Components/TableActivities"

export default class Loader {
  uid: string = 'customers';
  constructor(app: HubletoMain) {
    app.registerReactComponent('CoreCustomersTableCustomers', CoreCustomersTableCustomers);
    app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
  }
}
