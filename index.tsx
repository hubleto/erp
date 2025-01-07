import HubletoMain from './src/App';
import Billing from './apps/Billing/Loader'
import Calendar from './apps/Calendar/Loader'
import Customers from './apps/Customers/Loader'
import Documents from './apps/Documents/Loader'
import Invoices from './apps/Invoices/Loader'
import Settings from './apps/Settings/Loader'
import Leads from './apps/Leads/Loader'
import Deals from './apps/Deals/Loader'

//@ts-ignore
const app: HubletoMain = new HubletoMain(window.ConfigEnv);

app.registerModule(Billing);
app.registerModule(Calendar);
app.registerModule(Customers);
app.registerModule(Documents);
app.registerModule(Invoices);
app.registerModule(Settings);
app.registerModule(Leads);
app.registerModule(Deals);

// Render react elements into body
app.renderReactElements();

globalThis.app = app;
