import 'primereact/resources/themes/lara-light-teal/theme.css';

import { ADIOS } from "adios/Loader";
import request from "adios/Request";

// ADIOS
// import Table from "adios/Table";
import Modal from "adios/ModalSimple";
import InputVarchar from "adios/Inputs/Varchar";
import InputInt from "adios/Inputs/Int";
import InputLookup from "adios/Inputs/Lookup";
import InputImage from "adios/Inputs/Image";
import InputBoolean from "adios/Inputs/Boolean";
import InputColor from "adios/Inputs/Color";
import InputHyperlink from "adios/Inputs/Hyperlink";

// Hubleto
import HubletoForm from "./core/Components/HubletoForm";
import HubletoTable from "./core/Components/HubletoTable";

// Primereact
import { Tooltip } from "primereact/tooltip";

export default class HubletoMain extends ADIOS {
  language: string = 'en';
  idUser: number = 0;
  user: any;
  apps: any = {};

  constructor(config: object) {
    super(config);

    // ADIOS components
    // this.registerReactComponent('Table', Table);
    this.registerReactComponent('Modal', Modal);

    this.registerReactComponent('InputVarchar', InputVarchar);
    this.registerReactComponent('InputInt', InputInt);
    this.registerReactComponent('InputLookup', InputLookup);
    this.registerReactComponent('InputBoolean', InputBoolean);
    this.registerReactComponent('InputImage', InputImage);
    this.registerReactComponent('InputColor', InputColor);
    this.registerReactComponent('InputHyperlink', InputHyperlink);

    // Hubleto components
    this.registerReactComponent('Form', HubletoForm);
    this.registerReactComponent('Table', HubletoTable);

    // Primereact
    this.registerReactComponent('Tooltip', Tooltip);
  }

  loadDictionary(language: string) {
    if (language == 'en') return;

    this.language = language;

    request.get(
      'api/dictionary',
      { language: language },
      (data: any) => {
        this.dictionary = data;
        console.log('loaddict', this.dictionary);
      }
    );
  }

  addToDictionary(orig: string, context: string) {
    request.get(
      'api/dictionary',
      { language: this.language, addNew: { orig: orig, context: context } },
    );
  }

  registerApp(appClass) {
    const app = new appClass(this);
    if (!app.uid || app.uid == '') console.error(appClass + ': app\'s UID is empty.');
    this.apps[app.uid] = app
  }
}
