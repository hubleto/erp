import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableKeys from './Components/TableKeys'
import TablePermissions from './Components/TablePermissions'
import TableUsages from './Components/TableUsages'

class ApiApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ApiTableKeys', TableKeys);
    globalThis.hubleto.registerReactComponent('ApiTablePermissions', TablePermissions);
    globalThis.hubleto.registerReactComponent('ApiTableUsages', TableUsages);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Api', new ApiApp());
