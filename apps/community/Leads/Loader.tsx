import TableLeads from "./Components/TableLeads"
import LeadFormActivity from "./Components/LeadFormActivity"
import FormCustomerTopMenu from './Components/FormCustomerTopMenu'
import FormDealTopMenu from './Components/FormDealTopMenu'
import TableLevels from './Components/TableLevels'

globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);
globalThis.main.registerReactComponent('LeadsFormActivity', LeadFormActivity);
globalThis.main.registerReactComponent('LeadsTableLevels', TableLevels);

globalThis.main.registerDynamicContentInjector(
  'HubletoApp\\Community\\Customers\\Loader::Components\\FormCustomer:TopMenu',
  FormCustomerTopMenu
);

globalThis.main.registerDynamicContentInjector(
  'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal:TopMenu',
  FormDealTopMenu
);
