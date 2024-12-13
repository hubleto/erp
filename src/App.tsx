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

export default class CeremonyCrmApp extends ADIOS {
  language: string = 'en';
  idUser: number = 0;
  user: any;
  modules: any = {};

  constructor(config: object) {
    super(config);

    this.registerReactComponent('Table', Table);
    this.registerReactComponent('Modal', Modal);

    this.registerReactComponent('InputVarchar', InputVarchar);
    this.registerReactComponent('InputInt', InputInt);
    this.registerReactComponent('InputLookup', InputLookup);
    this.registerReactComponent('InputBoolean', InputBoolean);
    this.registerReactComponent('InputImage', InputImage);
    this.registerReactComponent('InputColor', InputColor);

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

  registerModule(mClass) {
    const module = new mClass(this);
    if (!module.uid || module.uid == '') console.error('app: module does not have uid');
    this.modules[module.uid] = module
  }
}
