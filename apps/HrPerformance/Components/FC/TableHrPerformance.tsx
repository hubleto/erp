import Table from '@hubleto/react-ui/components/fc/Table'
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'

interface TableHrPerformanceProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrPerformance';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableHrPerformance = (props: TableHrPerformanceProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  {...props}
/>

export default TableHrPerformance;