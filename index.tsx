import HubletoMain from './src/Main';
import Billing from './apps/community/Billing/Loader'
import Calendar from './apps/community/Calendar/Loader'
import Customers from './apps/community/Customers/Loader'
import Documents from './apps/community/Documents/Loader'
import Invoices from './apps/community/Invoices/Loader'
import Settings from './apps/community/Settings/Loader'
import Leads from './apps/community/Leads/Loader'
import Deals from './apps/community/Deals/Loader'

//@ts-ignore
const main: HubletoMain = new HubletoMain(window.ConfigEnv);

main.registerApp(Billing);
main.registerApp(Calendar);
main.registerApp(Customers);
main.registerApp(Documents);
main.registerApp(Invoices);
main.registerApp(Settings);
main.registerApp(Leads);
main.registerApp(Deals);

// Render react elements into body
main.renderReactElements();

globalThis.app = main; // ADIOS requires 'app' property
globalThis.main = main;
