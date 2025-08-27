import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import CustomersTableCustomers from "./Components/TableCustomers"
import CustomersTableActivities from "./Components/TableActivities"
import CustomersFormActivity from "./Components/CustomerFormActivity"

class CustomersApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('CustomersTableCustomers', CustomersTableCustomers);
    globalThis.main.registerReactComponent('CustomersTableActivities', CustomersTableActivities);
    globalThis.main.registerReactComponent('CustomersFormActivity', CustomersFormActivity);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Customers', new CustomersApp());
