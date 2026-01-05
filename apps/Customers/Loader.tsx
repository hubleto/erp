import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import CustomersTableCustomers from "./Components/TableCustomers"
import CustomersTableActivities from "./Components/TableActivities"
import CustomersFormActivity from "./Components/CustomerFormActivity"

class CustomersApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CustomersTableCustomers', CustomersTableCustomers);
    globalThis.hubleto.registerReactComponent('CustomersTableActivities', CustomersTableActivities);
    globalThis.hubleto.registerReactComponent('CustomersFormActivity', CustomersFormActivity);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Customers', new CustomersApp());
