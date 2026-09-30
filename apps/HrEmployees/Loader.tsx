import App from '@hubleto/react-ui/core/App'
import TableHrEmployees from './Components/FC/TableHrEmployees'
import TableHrEmployeesEmploymentTypes from './Components/FC/TableHrEmployeesEmploymentTypes'
import TableHrEmployeesEmploymentStatuses from './Components/FC/TableHrEmployeesEmploymentStatuses'
import TableHrEmployeesWorkLocations from './Components/FC/TableHrEmployeesWorkLocations'

class HrEmployeesApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrEmployeesTable', TableHrEmployees);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentTypes', TableHrEmployeesEmploymentTypes);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentStatuses', TableHrEmployeesEmploymentStatuses);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableWorkLocations', TableHrEmployeesWorkLocations);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrEmployees', new HrEmployeesApp());