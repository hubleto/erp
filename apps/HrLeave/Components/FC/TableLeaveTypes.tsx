import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormLeave from './FormLeave'

const componentName = 'TableLeaveTypes';
const parentApp = 'Hubleto/App/Community/HrLeave';

const TableLeaveTypes = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LeaveType'}
  baseUrlSlug='hr-leaves/types'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormLeave {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableLeaveTypes;