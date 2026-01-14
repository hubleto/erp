import App from '@hubleto/react-ui/core/App'
import TableDashboards from "./Components/TableDashboards"
import Dashboard from "./Components/Dashboard"

class DashboardsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DashboardsDashboard', Dashboard);
    globalThis.hubleto.registerReactComponent('DashboardsTableDashboards', TableDashboards);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Dashboards', new DashboardsApp());
