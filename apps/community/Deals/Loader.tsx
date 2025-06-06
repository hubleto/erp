import TableDeals from "./Components/TableDeals"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'

globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
