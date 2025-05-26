import TableDeals from "./Components/TableDeals"
import FormActivity from "./Components/FormActivity"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'

globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
globalThis.main.registerReactComponent('DealsFormActivity', FormActivity);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
