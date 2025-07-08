import TableDeals from "./Components/TableDeals"
import DealFormActivity from "./Components/DealFormActivity"
import FormCustomerTopMenu from './Components/FormCustomerTopMenu'

globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
globalThis.main.registerReactComponent('DealsFormActivity', DealFormActivity);

globalThis.main.registerDynamicContentInjector(
  'HubletoApp\\Community\\Customers\\Loader::Components\\FormCustomer:TopMenu',
  FormCustomerTopMenu
);
