import Table from '@hubleto/react-ui/components/fc/Table'
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'

interface TableHrLeaveProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrLeave';
const parentApp = 'Hubleto/App/Community/HrLeave';

const TableHrLeave = (props: TableHrLeaveProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  {...props}
/>

export default TableHrLeave;