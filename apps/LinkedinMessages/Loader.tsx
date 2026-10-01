import React, { Component } from 'react';
import App from '@hubleto/react-ui/core/App'
import TableMessages from "./Components/FC/TableMessages"
import TableProfiles from "./Components/FC/TableProfiles"
import TableAccounts from "./Components/FC/TableAccounts"
import TableCampaigns from "./Components/FC/TableCampaigns"
import TableTags from "./Components/FC/TableTags"
import TableReplies from "./Components/FC/TableReplies"
import TableMessageActivities from "./Components/FC/TableMessageActivities"
import MessageCalendarActivityForm from './Components/FC/MessageCalendarActivityForm';

class LinkedinMessagesApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableMessages', TableMessages);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableProfiles', TableProfiles);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableAccounts', TableAccounts);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableCampaigns', TableCampaigns);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableTags', TableTags);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableReplies', TableReplies);
    globalThis.hubleto.registerReactComponent('LinkedinMessagesTableMessageActivities', TableMessageActivities);
    globalThis.hubleto.registerReactComponent('MessageCalendarActivityForm', MessageCalendarActivityForm);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Custom/LinkedinMessages', new LinkedinMessagesApp());
