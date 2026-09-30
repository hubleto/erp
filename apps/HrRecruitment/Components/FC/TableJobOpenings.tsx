import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormJobOpening from './FormJobOpening'

const componentName = 'TableJobOpenings'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableJobOpenings = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/JobOpening'}
  baseUrlSlug='hr-recruitment/job-openings'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormJobOpening {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableJobOpenings;