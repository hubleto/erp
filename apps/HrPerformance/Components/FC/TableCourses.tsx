import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormCourse from './FormCourse'

const componentName = 'TableCourses';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableCourses = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Course'}
  baseUrlSlug='hr-performance/courses'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormCourse {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableCourses;