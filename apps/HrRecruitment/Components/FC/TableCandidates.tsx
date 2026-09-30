import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormCandidate from './FormCandidate'

const componentName = 'TableCandidates'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableCandidates = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Candidate'}
  baseUrlSlug='hr-recruitment/candidates'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormCandidate {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableCandidates;