import TableLeads from "./Components/TableLeads"
import TableCampaigns from "./Components/TableCampaigns"
import LeadFormActivity from "./Components/LeadFormActivity"
import FormCustomerExtraButtons from './Components/FormCustomerExtraButtons'
import TableLevels from './Components/TableLevels'

globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);
globalThis.main.registerReactComponent('LeadsFormActivity', LeadFormActivity);
globalThis.main.registerReactComponent('LeadsTableCampaigns', TableCampaigns);
globalThis.main.registerReactComponent('LeadsTableLevels', TableLevels);

globalThis.main.registerDynamicContentInjector('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', FormCustomerExtraButtons);
