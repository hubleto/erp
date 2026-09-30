import App from '@hubleto/react-ui/core/App'
import TableAttendances from './Components/FC/TableAttendances'
import TableShifts from './Components/FC/TableShifts'

class HrAttendanceApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrAttendanceTableAttendances', TableAttendances);
    globalThis.hubleto.registerReactComponent('HrAttendanceTableShifts', TableShifts);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrAttendance', new HrAttendanceApp());