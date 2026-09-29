import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployeesEmploymentType from './FormHrEmployeesEmploymentType'

const componentName = 'TableHrEmployeesEmploymentType'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableHrEmployeesEmploymentType = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  baseUrlSlug='hr-employees/employment-types'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployeesEmploymentType {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployeesEmploymentType
