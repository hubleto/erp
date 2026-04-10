import App from '@hubleto/react-ui/core/App'
import TableIssues from "./Components/TableIssues"
import TablePosts from "./Components/TablePosts"

class IssuesApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('IssuesTableIssues', TableIssues);
    globalThis.hubleto.registerReactComponent('IssuesTablePosts', TablePosts);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Issues', new IssuesApp());
