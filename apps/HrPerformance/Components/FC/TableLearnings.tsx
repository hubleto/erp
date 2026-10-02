import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormLearning from './FormLearning'

const componentName = 'TableLearnings';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableLearnings = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Learning'}
  baseUrlSlug='hr-performance/learnings'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormLearning {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableLearnings;