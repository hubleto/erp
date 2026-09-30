import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormGoal from './FormGoal'

const componentName = 'TableGoals';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableGoals = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Goal'}
  baseUrlSlug='hr-performance/goals'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormGoal {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableGoals;