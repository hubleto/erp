import { ADIOS } from "adios/Loader";
import request from "adios/Request";

// ADIOS
import Table from "adios/Table";
import Modal from "adios/ModalSimple";

import InputVarchar from "adios/Inputs/Varchar";
import InputInt from "adios/Inputs/Int";
import InputLookup from "adios/Inputs/Lookup";
import InputImage from "adios/Inputs/Image";
import InputBoolean from "adios/Inputs/Boolean";
import InputColor from "adios/Inputs/Color";

// Primereact
import { Tooltip } from "primereact/tooltip";

// Modules

// Module: Core/Billing
import CoreBillingTableBillingAccountService from "./modules/Core/Billing/Components/TableBillingAccountServices"

import CalendarComponent from "./modules/Core/Calendar/Components/CalendarComponent";

// Module: Core/Customers
import CoreCustomersTablePersons from "./modules/Core/Customers/Components/TablePersons"
import CoreCustomersTableCompanies from "./modules/Core/Customers/Components/TableCompanies"
import CoreCustomersTableActivities from "./modules/Core/Customers/Components/TableActivities"
import CoreCustomersTableAddresses from "./modules/Core/Customers/Components/TableAddresses"
import CoreCustomersTableContacts from "./modules/Core/Customers/Components/TableContacts"

// Module: Core/Documents
import CoreDocumentsTableDocuments from "./modules/Core/Documents/Components/TableDocuments"

// Module: Core/Invoices
import CoreInvoicesTableInvoices from "./modules/Core/Invoices/Components/TableInvoices"

// Module: Core/Settings
import CoreSettingsFormUser from "./modules/Core/Settings/Components/FormUser"
import CoreSettingsTablePipelines from "./modules/Core/Settings/Components/TablePipelines"
import CoreSettingsTableUserRoles from "./modules/Core/Settings/Components/TableUserRoles"

// Module: Sales/Leads
import SalesTableLeads from "./modules/Sales/Leads/Components/TableLeads"

// Module: Sales/Deals
import SalesTableDeals from "./modules/Sales/Deals/Components/TableDeals"






export class CeremonyCrmApp extends ADIOS {
  language: string = 'en';
  idUser: number = 0;
  user: any;

  loadDictionary(language: string) {
    if (language == 'en') return;

    this.language = language;

    request.get(
      'api/dictionary',
      { language: language },
      (data: any) => {
        this.dictionary = data;
        console.log(this.dictionary);
      }
    );
  }

  addToDictionary(orig: string, context: string) {
    request.get(
      'api/dictionary',
      { language: this.language, addNew: { orig: orig, context: context } },
    );
  }
}

//@ts-ignore
const app: CeremonyCrmApp = new CeremonyCrmApp(window.ConfigEnv);

// ADIOS components
app.registerReactComponent('Table', Table);
app.registerReactComponent('Modal', Modal);

app.registerReactComponent('InputVarchar', InputVarchar);
app.registerReactComponent('InputInt', InputInt);
app.registerReactComponent('InputLookup', InputLookup);
app.registerReactComponent('InputBoolean', InputBoolean);
app.registerReactComponent('InputImage', InputImage);
app.registerReactComponent('InputColor', InputColor);

// Primereact
app.registerReactComponent('Tooltip', Tooltip);

//rSuite
app.registerReactComponent('CalendarComponent', CalendarComponent);

// Modules

// Module: Core/Billing
app.registerReactComponent('CoreBillingTableBillingAccountService', CoreBillingTableBillingAccountService);

// Module: Core/Customers
app.registerReactComponent('CoreSettingsFormUser', CoreSettingsFormUser);
app.registerReactComponent('CoreCustomersTablePersons', CoreCustomersTablePersons);
app.registerReactComponent('CoreCustomersTableCompanies', CoreCustomersTableCompanies);
app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
app.registerReactComponent('CoreCustomersTableAddresses', CoreCustomersTableAddresses);
app.registerReactComponent('CoreCustomersTableContacts', CoreCustomersTableContacts);

// Module: Core/Documents
app.registerReactComponent('CoreDocumentsTableDocuments', CoreDocumentsTableDocuments);

// Module: Core/Invoices
app.registerReactComponent('CoreInvoicesTableInvoices', CoreInvoicesTableInvoices);

// Module: Core/Settings
app.registerReactComponent('CoreSettingsTablePipelines', CoreSettingsTablePipelines);
app.registerReactComponent('CoreSettingsTableUserRoles', CoreSettingsTableUserRoles);

// Module: Sale/Leads
app.registerReactComponent('SalesTableLeads', SalesTableLeads);

// Module: Sale/Deals
app.registerReactComponent('SalesTableDeals', SalesTableDeals);


// Render react elements into body
app.renderReactElements();

globalThis.app = app;
