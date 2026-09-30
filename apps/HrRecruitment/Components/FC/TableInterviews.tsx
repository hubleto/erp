import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormInterview from './FormInterview'

const componentName = 'TableInterviews'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableInterviews = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Interview'}
  baseUrlSlug='hr-recruitment/interviews'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormInterview {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableInterviews;