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
import FormUser from "./Modules/Core/Settings/FormUser"
import CoreCustomersTablePersons from "./Modules/Core/Customers/TablePersons"
import CoreCustomersTableCompanies from "./Modules/Core/Customers/TableCompanies"
import CoreCustomersTableActivities from "./Modules/Core/Customers/TableActivities"
import CoreSandboxTableCompanies from "./Modules/Core/Sandbox/TableCompanies"


//@ts-ignore
const app: ADIOS = new ADIOS(window.ConfigEnv);

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
app.registerReactComponent('FormUser', FormUser);
app.registerReactComponent('CoreCustomersTablePersons', CoreCustomersTablePersons);
app.registerReactComponent('CoreCustomersTableCompanies', CoreCustomersTableCompanies);
app.registerReactComponent('CoreCustomersTableActivities', CoreCustomersTableActivities);
app.registerReactComponent('CoreSandboxTableCompanies', CoreSandboxTableCompanies);

// Render react elements into body
app.renderReactElements();

globalThis.app = app;
