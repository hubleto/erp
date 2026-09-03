import App from '@hubleto/react-ui/core/App'
import TableWorkflows from "./Components/FC/TableWorkflows"
import TableWorkflowSteps from "./Components/FC/TableWorkflowSteps"
import TableAutomats from "./Components/FC/TableAutomats"

class Workflow extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('WorkflowTableWorkflows', TableWorkflows);
    globalThis.hubleto.registerReactComponent('WorkflowTableWorkflowSteps', TableWorkflowSteps);
    globalThis.hubleto.registerReactComponent('WorkflowTableAutomats', TableAutomats);

  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Enterprise/Workflow', new Workflow());



