import App from '@hubleto/react-ui/core/App'
import TableHrEmployees from './Components/FC/TableHrEmployees'
import TableHrEmployeesEmploymentType from './Components/FC/TableHrEmployeesEmploymentType'
import TableHrEmployeesEmploymentStatus from './Components/FC/TableHrEmployeesEmploymentStatus'
import TableHrEmployeesWorkLocation from './Components/FC/TableHrEmployeesWorkLocation'

class HrEmployeesApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrEmployeesTable', TableHrEmployees);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentType', TableHrEmployeesEmploymentType);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentStatus', TableHrEmployeesEmploymentStatus);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableWorkLocation', TableHrEmployeesWorkLocation);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrEmployees', new HrEmployeesApp());