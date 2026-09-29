import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrAttendanceRecord from './FormHrAttendanceRecord'
import FormHrShift from './FormHrShift'

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
  renderForm={(table: TableMeta) => {
    const formProps = table.getDefaultFormProps();
    return props.model === parentApp + '/Models/Shift'
      ? <FormHrShift {...formProps} />
      : <FormHrAttendanceRecord {...formProps} />;
  }}
  {...props}
/>

export default TableHrAttendance;