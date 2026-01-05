import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDashboards from "./Components/TableDashboards"
import Dashboard from "./Components/Dashboard"

class DashboardsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DashboardsDashboard', Dashboard);
    globalThis.hubleto.registerReactComponent('DashboardsTableDashboards', TableDashboards);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Dashboards', new DashboardsApp());
