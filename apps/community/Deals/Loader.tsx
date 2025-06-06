import TableDeals from "./Components/TableDeals"
import DealFormActivity from "./Components/DealFormActivity"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'

globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
globalThis.main.registerReactComponent('DealsFormActivity', DealFormActivity);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
