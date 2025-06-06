import TableLeads from "./Components/TableLeads"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'

globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
