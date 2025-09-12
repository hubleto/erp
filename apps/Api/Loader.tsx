import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableKeys from './Components/TableKeys'
import TablePermissions from './Components/TablePermissions'
import TableUsages from './Components/TableUsages'

class ApiApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('ApiTableKeys', TableKeys);
    globalThis.main.registerReactComponent('ApiTablePermissions', TablePermissions);
    globalThis.main.registerReactComponent('ApiTableUsages', TableUsages);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Api', new ApiApp());
