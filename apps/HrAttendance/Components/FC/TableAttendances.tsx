import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormAttendance from './FormAttendance'

const componentName = 'TableAttendance';
const parentApp = 'Hubleto/App/Community/HrAttendance';

const TableAttendances = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Attendance'}
  baseUrlSlug='hr-attendance'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormAttendance {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableAttendances;