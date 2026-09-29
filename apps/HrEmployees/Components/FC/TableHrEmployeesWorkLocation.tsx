import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployeesWorkLocation from './FormHrEmployeesWorkLocation'

const componentName = 'TableHrEmployeesWorkLocation'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableHrEmployeesWorkLocation = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/WorkLocation'}
  baseUrlSlug='hr-employees/work-locations'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployeesWorkLocation {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployeesWorkLocation
