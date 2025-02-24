import HubletoMain from './src/Main';
import Billing from './apps/community/Billing/Loader'
import Calendar from './apps/community/Calendar/Loader'
import Customers from './apps/community/Customers/Loader'
import Contacts from './apps/community/Contacts/Loader'
import Documents from './apps/community/Documents/Loader'
import Invoices from './apps/community/Invoices/Loader'
import Settings from './apps/community/Settings/Loader'
import Leads from './apps/community/Leads/Loader'
import Deals from './apps/community/Deals/Loader'
import Products from './apps/community/Products/Loader'
import Orders from './apps/community/Orders/Loader'
import Report from './apps/community/Reports/Loader'
import CalendarSync from './apps/community/CalendarSync/Loader'
import Goals from './apps/community/Goals/Loader'

//@ts-ignore
const main: HubletoMain = new HubletoMain(window.ConfigEnv);

main.registerApp(Billing);
main.registerApp(Calendar);
main.registerApp(Customers);
main.registerApp(Contacts);
main.registerApp(Documents);
main.registerApp(Invoices);
main.registerApp(Settings);
main.registerApp(Leads);
main.registerApp(Deals);
main.registerApp(Products);
main.registerApp(Orders);
main.registerApp(Report);
main.registerApp(CalendarSync);
main.registerApp(Goals);

// Render react elements into body
main.renderReactElements();

globalThis.app = main; // ADIOS requires 'app' property
globalThis.main = main;
