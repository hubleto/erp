import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDashboards from "./Components/TableDashboards"
import Dashboard from "./Components/Dashboard"

class DashboardsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('DashboardsDashboard', Dashboard);
    globalThis.main.registerReactComponent('DashboardsTableDashboards', TableDashboards);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Dashboards', new DashboardsApp());
