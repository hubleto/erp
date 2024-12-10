import { ADIOS } from "adios/Loader";
import request from "adios/Request";

import { dictionarySk } from "adios/Dictionary/Sk";

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

// rSuite
import CalendarComponent from "./Modules/Core/Calendar/CalendarComponent";

// Modules

// Module: Core/Billing
import CoreBillingTableBillingAccountService from "./Modules/Core/Billing/TableBillingAccountServices"

// Module: Core/Customers
import CoreCustomersTablePersons from "./Modules/Core/Customers/TablePersons"
import CoreCustomersTableCompanies from "./Modules/Core/Customers/TableCompanies"
import CoreCustomersTableActivities from "./Modules/Core/Customers/TableActivities"
import CoreCustomersTableAddresses from "./Modules/Core/Customers/TableAddresses"
import CoreCustomersTableContacts from "./Modules/Core/Customers/TableContacts"

// Module: Core/Documents
import CoreDocumentsTableDocuments from "./Modules/Core/Documents/TableDocuments"

// Module: Core/Invoices
import CoreInvoicesTableInvoices from "./Modules/Core/Invoices/TableInvoices"

// Module: Core/Settings
import CoreSettingsFormUser from "./Modules/Core/Settings/FormUser"
import CoreSettingsTablePipelines from "./Modules/Core/Settings/TablePipelines"
import CoreSettingsTableUserRoles from "./Modules/Core/Settings/TableUserRoles"

// Module: Sales/Leads
import SalesTableLeads from "./Modules/Sales/TableLeads"

// Module: Sales/Deals
import SalesTableDeals from "./Modules/Sales/TableDeals"






export class CeremonyCrmApp extends ADIOS {
  idUser: number = 0;
  user: any;

  loadDictionary(language: string) {
    if (language == 'en') return;

    request.get(
      'api/dictionary',
      { language: language },
      (data: any) => {
        console.log(data);
        this.dictionary = data;

        if (language == 'sk') this.dictionary = { ...this.dictionary, _default_: dictionarySk };

        console.log(this.dictionary);
      }
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
