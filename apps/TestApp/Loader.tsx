import App from '@hubleto/react-ui/core/App'
import CustomersTableCustomers from "./Components/TableCustomers"
import CustomersTableActivities from "./Components/TableActivities"
import CustomersFormActivity from "./Components/CustomerFormActivity"

class TestApp extends App {
  init() {
    super.init();

    // register react components
    // globalThis.hubleto.registerReactComponent('CustomersTableCustomers', CustomersTableCustomers);
    // globalThis.hubleto.registerReactComponent('CustomersTableActivities', CustomersTableActivities);
    // globalThis.hubleto.registerReactComponent('CustomersFormActivity', CustomersFormActivity);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/TestApp', new TestApp());
