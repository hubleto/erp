import TableLeads from "./Components/TableLeads"
import FormActivity from "./Components/FormActivity"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'

globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);
globalThis.main.registerReactComponent('LeadsFormActivity', FormActivity);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
