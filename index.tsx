import CeremonyCrmApp from './src/App';
import Billing from '../src/apps/Billing/Loader'
import Calendar from '../src/apps/Calendar/Loader'
import Customers from '../src/apps/Customers/Loader'
import Documents from '../src/apps/Documents/Loader'
import Invoices from '../src/apps/Invoices/Loader'
import Settings from '../src/apps/Settings/Loader'
import Leads from '../src/apps/Leads/Loader'
import Deals from '../src/apps/Deals/Loader'

//@ts-ignore
const app: CeremonyCrmApp = new CeremonyCrmApp(window.ConfigEnv);

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
