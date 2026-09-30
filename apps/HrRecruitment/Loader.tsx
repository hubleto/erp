import App from '@hubleto/react-ui/core/App'
import TableApplications from './Components/FC/TableApplications'
import TableCandidates from './Components/FC/TableCandidates'
import TableEmploymentTypes from './Components/FC/TableEmploymentTypes'
import TableInterviews from './Components/FC/TableInterviews'
import TableJobOpenings from './Components/FC/TableJobOpenings'
import TableWorkLocations from './Components/FC/TableWorkLocations'

class HrRecruitmentApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableApplications', TableApplications);
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableCandidates', TableCandidates);
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableEmploymentTypes', TableEmploymentTypes);
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableInterviews', TableInterviews);
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableJobOpenings', TableJobOpenings);
    globalThis.hubleto.registerReactComponent('HrRecruitmentTableWorkLocations', TableWorkLocations);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrRecruitment', new HrRecruitmentApp());