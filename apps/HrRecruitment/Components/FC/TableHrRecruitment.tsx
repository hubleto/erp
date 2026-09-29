import Table from '@hubleto/react-ui/components/fc/Table'
import { TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'

interface TableHrRecruitmentProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrRecruitment';
const parentApp = 'Hubleto/App/Community/HrRecruitment';

const TableHrRecruitment = (props: TableHrRecruitmentProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  {...props}
/>

export default TableHrRecruitment;