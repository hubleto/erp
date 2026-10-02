import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormApplication from './FormApplication'

const componentName = 'TableApplications'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableApplications = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Application'}
  baseUrlSlug='hr-recruitment/applications'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormApplication {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableApplications;