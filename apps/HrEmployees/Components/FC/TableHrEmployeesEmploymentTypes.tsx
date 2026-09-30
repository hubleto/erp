import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployeesEmploymentType from './FormHrEmployeesEmploymentType'

const componentName = 'TableHrEmployeesEmploymentTypes'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableHrEmployeesEmploymentTypes = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  baseUrlSlug='hr-employees/employment-types'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployeesEmploymentType {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployeesEmploymentTypes;
