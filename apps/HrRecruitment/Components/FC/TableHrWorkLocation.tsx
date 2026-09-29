import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrWorkLocation from './FormHrWorkLocation'

const componentName = 'TableHrWorkLocation'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableHrWorkLocation = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/WorkLocation'}
  baseUrlSlug='hr-recruitment/work-locations'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrWorkLocation {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrWorkLocation