import Table from '@hubleto/react-ui/components/fc/Table'
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'

interface TableHrAttendanceProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrAttendance';
const parentApp = 'Hubleto/App/Community/HrAttendance';

const TableHrAttendance = (props: TableHrAttendanceProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  {...props}
/>

export default TableHrAttendance;