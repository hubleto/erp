import App from '@hubleto/react-ui/core/App'
import TableHrRecruitment from './Components/FC/TableHrRecruitment'
import TableHrEmploymentType from './Components/FC/TableHrEmploymentType'
import TableHrWorkLocation from './Components/FC/TableHrWorkLocation'
import TableHrOpeningDate from './Components/FC/TableHrOpeningDate'

class HrRecruitmentApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrRecruitmentTable', TableHrRecruitment);
    globalThis.hubleto.registerReactComponent('HrRecruitmentEmploymentTypeTable', TableHrEmploymentType);
    globalThis.hubleto.registerReactComponent('HrRecruitmentWorkLocationTable', TableHrWorkLocation);
    globalThis.hubleto.registerReactComponent('HrRecruitmentOpeningDateTable', TableHrOpeningDate);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrRecruitment', new HrRecruitmentApp());