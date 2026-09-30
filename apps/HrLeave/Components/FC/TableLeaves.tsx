import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormLeave from './FormLeave'

const componentName = 'TableLeaves';
const parentApp = 'Hubleto/App/Community/HrLeave';

const TableLeaves = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Leave'}
  baseUrlSlug='hr-leaves'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormLeave {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableLeaves;