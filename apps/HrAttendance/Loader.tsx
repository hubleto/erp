import App from '@hubleto/react-ui/core/App'
import TableHrAttendance from './Components/FC/TableHrAttendance'

class HrAttendanceApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrAttendanceTable', TableHrAttendance);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrAttendance', new HrAttendanceApp());