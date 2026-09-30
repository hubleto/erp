import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormLeave from './FormLeave'

const componentName = 'TableLeaveRequests';
const parentApp = 'Hubleto/App/Community/HrLeave';

const TableLeaveRequests = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LeaveRequest'}
  baseUrlSlug='hr-leaves/requests'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormLeave {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableLeaveRequests;