import App from '@hubleto/react-ui/core/App'
import CustomersTableCustomers from "./Components/FC/TableCustomers"
// import CustomersTableActivities from "./Components/TableActivities"
import CustomersFormActivity from "./Components/FC/CustomerFormActivity"

class CustomersApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CustomersTableCustomers', CustomersTableCustomers);
    // globalThis.hubleto.registerReactComponent('CustomersTableActivities', CustomersTableActivities);
    globalThis.hubleto.registerReactComponent('CustomersCustomerFormActivity', CustomersFormActivity);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Customers', new CustomersApp());
