import CeremonyCrmApp from './src/App';
import Billing from './src/modules/Billing/Loader'
import Calendar from './src/modules/Calendar/Loader'
import Customers from './src/modules/Customers/Loader'
import Documents from './src/modules/Documents/Loader'
import Invoices from './src/modules/Invoices/Loader'
import Settings from './src/modules/Settings/Loader'
import Leads from './src/modules/Leads/Loader'
import Deals from './src/modules/Deals/Loader'

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
