import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormEmploymentStatus from './FormEmploymentStatus'

const componentName = 'TableEmploymentStatuses'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableEmploymentStatuses = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentStatus'}
  baseUrlSlug='hr-employees/employment-statuses'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormEmploymentStatus {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableEmploymentStatuses;
