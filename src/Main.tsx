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
import HubletoChart from "./core/Components/HubletoChart";

// Primereact
import { Tooltip } from "primereact/tooltip";

class HubletoMain extends ADIOS {
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
    this.registerReactComponent('Chart', HubletoChart);

    // Primereact
    this.registerReactComponent('Tooltip', Tooltip);
  }

  translate(orig: string, context?: string): string {
    let translated: string = orig;

    let tmp = (context ?? '').split('::');
    const contextClass = tmp[0];
    const contextInner = tmp[1];

    // console.log('translate', contextClass, contextInner, orig, this.dictionary);

    if (this.dictionary === null) return orig;

    if (this.dictionary[contextClass] && this.dictionary[contextClass][contextInner]) {
      translated = this.dictionary[contextClass][contextInner][orig] ?? '';
    } else {
      translated = '';
      this.addToDictionary(orig, context);
    }

    if (translated == '') translated = context + '#' + orig;

    return translated;
  }

  loadDictionary(language: string) {
    if (language == 'en') return;

    this.language = language;

    request.get(
      'api/dictionary',
      { language: language },
      (data: any) => {
        this.dictionary = data;
      }
    );
  }

  addToDictionary(orig: string, context: string) {
    request.get(
      'api/dictionary',
      { language: this.language, addNew: { orig: orig, context: context } },
    );
  }

  registerApp(appUid, appClass) {
    // console.log('registeringApp', appUid, appClass);
    this.apps[appUid] = new appClass(this);
  }

  createThemeObserver() {
    // MutationObserver looks for changes in DOM. Anytime a change is detected,
    // a light or dark theme is applied to changed or newly created DOM elements.
    (new MutationObserver((mutations, observer) => {
      if (localStorage.theme == "dark") {
        // Whenever the user explicitly chooses light mode
        $('*').addClass('dark');
      } else {
        // Whenever the user explicitly chooses dark mode
        $('*').removeClass('dark');
      }
    })).observe(document, { subtree: true, attributes: true });
  }

  startConsoleErrorLogger() {
    console.log('Hubleto: Starting console.error debugger.');
    if (window.console && console.error) {
      const ce = console.error;
      console.error = function() {
        request.post(
          'api/log-javascript-error',
          {},
          { errorRoute: '{{ route }}', errors: arguments }
        );
        ce.apply(this, arguments)
      }
    }
  }
}

//@ts-ignore
const main: HubletoMain = new HubletoMain(window.ConfigEnv);

globalThis.app = main; // ADIOS requires 'app' property
globalThis.main = main;
