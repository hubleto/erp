import HubletoMain from './src/Main'
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

globalThis.app = main; // ADIOS requires 'app' property
globalThis.main = main;

globalThis.main.registerApp('Billing', Billing);
globalThis.main.registerApp('Calendar', Calendar);
globalThis.main.registerApp('Customers', Customers);
globalThis.main.registerApp('Contacts', Contacts);
globalThis.main.registerApp('Documents', Documents);
globalThis.main.registerApp('Invoices', Invoices);
globalThis.main.registerApp('Settings', Settings);
globalThis.main.registerApp('Leads', Leads);
globalThis.main.registerApp('Deals', Deals);
globalThis.main.registerApp('Products', Products);
globalThis.main.registerApp('Orders', Orders);
globalThis.main.registerApp('Report', Report);
globalThis.main.registerApp('CalendarSync', CalendarSync);
globalThis.main.registerApp('Goals', Goals);

console.log('apps', globalThis.main.apps);

// Render react elements into HTML body
globalThis.main.renderReactElements();
