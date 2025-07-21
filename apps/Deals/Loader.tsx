import TableDeals from "./Components/TableDeals"
import DealFormActivity from "./Components/DealFormActivity"
import FormCustomerTopMenu from './Components/FormCustomerTopMenu'
import FormLeadTopMenu from './Components/FormLeadTopMenu'

globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
globalThis.main.registerReactComponent('DealsFormActivity', DealFormActivity);

globalThis.main.registerDynamicContent('FormCustomer:TopMenu', FormCustomerTopMenu);
globalThis.main.registerDynamicContent('FormLead:TopMenu', FormLeadTopMenu);
