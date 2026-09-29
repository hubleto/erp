import App from '@hubleto/react-ui/core/App'
import TableHrEmployees from './Components/FC/TableHrEmployees'

class HrEmployeesApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrEmployeesTable', TableHrEmployees);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrEmployees', new HrEmployeesApp());