import App from '@hubleto/react-ui/core/App'
import TableWorkflows from "./Components/TableWorkflows"
import TableAutomats from "./Components/TableAutomats"

class Workflow extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('WorkflowTableWorkflows', TableWorkflows);
    globalThis.hubleto.registerReactComponent('WorkflowTableAutomats', TableAutomats);

  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Enterprise/Workflow', new Workflow());



