import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrLeaveBalance from './FormHrLeaveBalance'
import FormHrLeaveRequest from './FormHrLeaveRequest'
import FormHrLeaveType from './FormHrLeaveType'

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
  renderForm={(table: TableMeta) => {
    const formProps = table.getDefaultFormProps();
    switch (props.model) {
      case parentApp + '/Models/LeaveType': return <FormHrLeaveType {...formProps} />;
      case parentApp + '/Models/LeaveBalance': return <FormHrLeaveBalance {...formProps} />;
      default: return <FormHrLeaveRequest {...formProps} />;
    }
  }}
  {...props}
/>

export default TableHrLeave;