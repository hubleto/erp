import { ADIOS } from "adios/Loader";
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
//core
import FormUser from "./Modules/Core/Settings/FormUser"
import CoreCustomersTablePersons from "./Modules/Core/Customers/TablePersons"
import CoreCustomersTableCompanies from "./Modules/Core/Customers/TableCompanies"
import CoreCustomersTableActivities from "./Modules/Core/Customers/TableActivities"
import CoreCustomersTableAddresses from "./Modules/Core/Customers/TableAddresses"
import CoreCustomersTableContacts from "./Modules/Core/Customers/TableContacts"
import CoreSettingsTablePipelines from "./Modules/Core/Settings/TablePipelines"
import CoreSettingsTableUserRoles from "./Modules/Core/Settings/TableUserRoles"

//billing
import CoreBillingTableBillingAccountService from "./Modules/Core/Billing/TableBillingAccountServices"

//sales
import SalesTableLeads from "./Modules/Sales/TableLeads"
import SalesTableDeals from "./Modules/Sales/TableDeals"

//documents
import CoreDocumentsTableDocuments from "./Modules/Core/Documents/TableDocuments"

//@ts-ignore
const app: ADIOS = new ADIOS(window.ConfigEnv);

export class CeremonyCrmApp extends ADIOS {
    /* static ROLE_ADMINISTRATOR: number = 1;
    static ROLE_ADVOKAT: number = 2;
    static ROLE_MANAZER: number = 3;
    static ROLE_KONCIPIENT: number = 4; */

    language: string = 'en';
    idUser: number = 0;
    user: any;

    /* userHasRole(idRole: number) {
      let has = false;
      if (this.user.roles) {
        for (let i in this.user.roles) {
          if (this.user.roles[i] == idRole) has = true;
        }
      }
      return has;
    } */
  }

app.dictionary.sk = dictionarySk;

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
//core
app.registerReactComponent('FormUser', FormUser);
app.registerReactComponent('CoreCustomersTablePersons', CoreCustomersTablePersons);
app.registerReactComponent('CoreCustomersTableCompanies', CoreCustomersTableCompanies);
app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
app.registerReactComponent('CoreCustomersTableAddresses', CoreCustomersTableAddresses);
app.registerReactComponent('CoreCustomersTableContacts', CoreCustomersTableContacts);
app.registerReactComponent('CoreSettingsTablePipelines', CoreSettingsTablePipelines);
app.registerReactComponent('CoreSettingsTableUserRoles', CoreSettingsTableUserRoles);
//documents
app.registerReactComponent('CoreDocumentsTableDocuments', CoreDocumentsTableDocuments);
//billing
app.registerReactComponent('CoreBillingTableBillingAccountService', CoreBillingTableBillingAccountService);
//sales
app.registerReactComponent('SalesTableLeads', SalesTableLeads);
app.registerReactComponent('SalesTableDeals', SalesTableDeals);


// Render react elements into body
app.renderReactElements();

globalThis.app = app;
