import $ from 'jquery';
import React, { Component } from 'react';
import 'primereact/resources/themes/lara-light-teal/theme.css';

import { HubletoReactUi } from "@hubleto/react-ui/core/Loader";
import request from "@hubleto/react-ui/core/Request";

import Modal from "@hubleto/react-ui/core/ModalSimple";
import InputVarchar from "@hubleto/react-ui/core/Inputs/Varchar";
import InputInt from "@hubleto/react-ui/core/Inputs/Int";
import InputLookup from "@hubleto/react-ui/core/Inputs/Lookup";
import InputImage from "@hubleto/react-ui/core/Inputs/Image";
import InputBoolean from "@hubleto/react-ui/core/Inputs/Boolean";
import InputColor from "@hubleto/react-ui/core/Inputs/Color";
import InputHyperlink from "@hubleto/react-ui/core/Inputs/Hyperlink";
import InputWysiwyg from "@hubleto/react-ui/core/Inputs/Wysiwyg";

import TableCellRendererHyperlink from "@hubleto/react-ui/core/TableCellRenderers/Hyperlink";

// Hubleto
import HubletoSearch from "@hubleto/react-ui/ext/HubletoSearch";
import HubletoForm from "@hubleto/react-ui/ext/HubletoForm";
import HubletoTable from "@hubleto/react-ui/ext/HubletoTable";
import HubletoChart from "@hubleto/react-ui/ext/HubletoChart";
import HubletoTableColumnCustomize from "@hubleto/react-ui/ext/HubletoTableColumnsCustomize";

// Primereact
import { Tooltip } from "primereact/tooltip";

class HubletoMain extends HubletoReactUi {
  language: string = 'en';
  idUser: number = 0;
  isPremium: boolean = false;
  user: any;
  apps: any = {};
  dynamicContentInjectors: any = {};

  constructor(config: object) {
    super(config);

    this.idUser = config['idUser'];
    this.isPremium = config['isPremium'];
    this.language = config['language'];
    this.loadDictionary(config['language']);

    this.registerReactComponent('Modal', Modal);

    this.registerReactComponent('InputVarchar', InputVarchar);
    this.registerReactComponent('InputInt', InputInt);
    this.registerReactComponent('InputLookup', InputLookup);
    this.registerReactComponent('InputBoolean', InputBoolean);
    this.registerReactComponent('InputImage', InputImage);
    this.registerReactComponent('InputColor', InputColor);
    this.registerReactComponent('InputHyperlink', InputHyperlink);
    this.registerReactComponent('InputWysiwyg', InputWysiwyg);

    this.registerReactComponent('TableCellRendererHyperlink', TableCellRendererHyperlink);

    // Hubleto components
    this.registerReactComponent('Search', HubletoSearch);
    this.registerReactComponent('Form', HubletoForm);
    this.registerReactComponent('Table', HubletoTable);
    this.registerReactComponent('TableColumnsCustomize', HubletoTableColumnCustomize);
    this.registerReactComponent('Chart', HubletoChart);

    // Primereact
    this.registerReactComponent('Tooltip', Tooltip);
  }

  translate(orig: string, context?: string): string {
    let translated: string = orig;

    let tmp = (context ?? '').split('::');
    const contextClass = tmp[0];
    const contextInner = tmp[1];

    // console.log('translate', contextClass, contextInner, orig, this.dictionary, this.dictionary[contextClass], this.dictionary[contextClass][contextInner]);

    if (this.dictionary === null) return orig;

    if (
      this.dictionary[contextClass]
      && this.dictionary[contextClass][contextInner]
      && this.dictionary[contextClass][contextInner][orig]
      && this.dictionary[contextClass][contextInner][orig] != ''
    ) {
      console.log('tr1');
      translated = this.dictionary[contextClass][contextInner][orig] ?? '';
    } else {
      console.log('tr2');
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

  numberFormat(
    value: string,
    decimals: number = 2,
    decimalSeparator: string = ',',
    thousandsSeparator: string = ' '
  ): string {
    value = (value ?? '').toString().replace(/[^0-9+\-Ee.]/g, '');

    let n = parseFloat(value);

    n = Math.round(n * Math.pow(10, decimals)) / Math.pow(10, decimals);
    let [integerPart, fractionalPart] = n.toString().split('.');
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

    if (fractionalPart === undefined) {
      fractionalPart = '';
    }
    while (fractionalPart.length < decimals) {
      fractionalPart += '0';
    }

    return integerPart + (decimals > 0 ? decimalSeparator + fractionalPart : '');
  }

  registerDynamicContent(contentGroup: string, injector: any) {
    if (!this.dynamicContentInjectors[contentGroup]) {
      this.dynamicContentInjectors[contentGroup] = [];
    }

    this.dynamicContentInjectors[contentGroup].push(injector);
  }

  injectDynamicContent(contentGroup: string, injectorProps: any): Array<JSX.Element>|null {
    if (this.dynamicContentInjectors && this.dynamicContentInjectors[contentGroup]) {
      let dynamicContent: Array<JSX.Element> = [];
      for (let i in this.dynamicContentInjectors[contentGroup]) {
        dynamicContent.push(
          React.createElement(
            this.dynamicContentInjectors[contentGroup][i],
            injectorProps
          )
        );
      }
      return dynamicContent;
      // return dynamicContent.map((content, key) => <div key={key}>{content}</div>);
    } else {
      return null;
    }
  }

}

//@ts-ignore
const main: HubletoMain = new HubletoMain(window.ConfigEnv);

globalThis.main = main;

document.addEventListener('readystatechange', function() {
  if (document.readyState === 'complete') {
    globalThis.main.renderReactElements();
    globalThis.main.createThemeObserver();
  }
});

document.addEventListener('keydown', function(e) {
  if (e.ctrlKey && e.key === 'k') {
    globalThis.main.reactElements['global-fulltext-search'].searchRef.current.focus();
    e.preventDefault();
  }
});
