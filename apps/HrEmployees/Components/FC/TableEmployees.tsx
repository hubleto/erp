import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormEmployee from './FormEmployee'

const componentName = 'TableEmployees';
const parentApp = 'Hubleto/App/Community/HrEmployees';

const TableEmployees = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Employee'}
  baseUrlSlug='hr-employees'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormEmployee {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableEmployees;