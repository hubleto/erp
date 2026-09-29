import Table from '@hubleto/react-ui/components/fc/Table'
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'

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
  {...props}
/>

export default TableHrEmployees;