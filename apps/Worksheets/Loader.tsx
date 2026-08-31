import React from 'react';
import App from '@hubleto/react-ui/core/App'
import TableActivities from './Components/FC/TableActivities';
import TableActivityTypes from './Components/FC/TableActivityTypes';
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';

class WorksheetsApp extends App {
  init() {
    super.init();

    globalThis.hubleto.registerReactComponent('WorksheetsTableActivities', TableActivities);
    globalThis.hubleto.registerReactComponent('WorksheetsTableActivityTypes', TableActivityTypes);

    FormCustomizer.addFormHeaderExtraButton(
      'FormTask',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: 'Add activity',
        icon: 'fas fa-user-clock',
        onClick: (form: FormMeta) => {
          globalThis.window.open(globalThis.hubleto.config.projectUrl + '/worksheets/add?idTask=' + form.id);
        },
      }}
    );
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Worksheets', new WorksheetsApp());
