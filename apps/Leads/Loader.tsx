import React, { Component } from 'react';
import App from '@hubleto/react-ui/core/App'
import TableLeads from "./Components/TableLeads"
import LeadFormActivity from "./Components/LeadFormActivity"
import TableLevels from './Components/TableLevels'

class LeadsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('LeadsTableLeads', TableLeads);
    globalThis.hubleto.registerReactComponent('LeadsTableLevels', TableLevels);
    globalThis.hubleto.registerReactComponent('LeadFormActivity', LeadFormActivity);

  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Leads', new LeadsApp());
