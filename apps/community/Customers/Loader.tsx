// import HubletoMain from "@hubleto/src/Main";
import CustomersTableCustomers from "./Components/TableCustomers"
import CustomersTableActivities from "./Components/TableActivities"

export default class Loader {
  constructor() {
    globalThis.main.registerReactComponent('CustomersTableCustomers', CustomersTableCustomers);
    globalThis.main.registerReactComponent('CustomersTableActivities', CustomersTableActivities);
  }
}
