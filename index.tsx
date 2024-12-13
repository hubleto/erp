import CeremonyCrmApp from './src/App';
import Billing from './src/modules/Core/Billing/Loader'
import Calendar from './src/modules/Core/Calendar/Loader'
import Customers from './src/modules/Core/Customers/Loader'
import Documents from './src/modules/Core/Documents/Loader'
import Invoices from './src/modules/Core/Invoices/Loader'
import Settings from './src/modules/Core/Settings/Loader'
import Leads from './src/modules/Sales/Leads/Loader'
import Deals from './src/modules/Sales/Deals/Loader'

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
