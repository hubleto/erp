import HubletoMain from './src/Main';
import Billing from './apps/Billing/Loader'
import Calendar from './apps/Calendar/Loader'
import Customers from './apps/Customers/Loader'
import Documents from './apps/Documents/Loader'
import Invoices from './apps/Invoices/Loader'
import Settings from './apps/Settings/Loader'
import Leads from './apps/Leads/Loader'
import Deals from './apps/Deals/Loader'

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
