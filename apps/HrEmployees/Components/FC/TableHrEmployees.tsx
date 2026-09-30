import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployee from './FormHrEmployee'

const componentName = 'TableHrEmployees';
const parentApp = 'Hubleto/App/Community/HrEmployees';

const TableHrEmployees = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Employee'}
  baseUrlSlug='hr-employees'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployee {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployees;