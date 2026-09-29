import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmployee from './FormHrEmployee'

interface TableHrEmployeesProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrEmployees';
const parentApp = 'Hubleto/App/Community/HrEmployees';

const TableHrEmployees = (props: TableHrEmployeesProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmployee {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmployees;