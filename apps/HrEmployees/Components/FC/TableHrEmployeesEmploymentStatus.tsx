import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployeesEmploymentStatus from './FormHrEmployeesEmploymentStatus'

const componentName = 'TableHrEmployeesEmploymentStatus'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableHrEmployeesEmploymentStatus = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentStatus'}
  baseUrlSlug='hr-employees/employment-statuses'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployeesEmploymentStatus {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployeesEmploymentStatus
