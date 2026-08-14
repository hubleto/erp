import App from '@hubleto/react-ui/core/App'
import TableKeys from './Components/FC/TableKeys'
import TablePermissions from './Components/FC/TablePermissions'
import TableUsages from './Components/FC/TableUsages'

class ApiApp extends App {
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
