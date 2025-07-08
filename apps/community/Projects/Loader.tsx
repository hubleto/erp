import TableProjects from "./Components/TableProjects"
import TablePhases from './Components/TablePhases'
import FormDealTopMenu from './Components/FormDealTopMenu'

globalThis.main.registerReactComponent('ProjectsTableProjects', TableProjects);
globalThis.main.registerReactComponent('ProjectsTablePhases', TablePhases);

globalThis.main.registerDynamicContent('FormDeal:TopMenu', FormDealTopMenu);
