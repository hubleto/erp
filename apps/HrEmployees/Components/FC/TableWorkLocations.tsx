import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormWorkLocation from './FormWorkLocation'

const componentName = 'TableWorkLocations'
const parentApp = 'Hubleto/App/Community/HrEmployees'

const TableWorkLocations = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/WorkLocation'}
  baseUrlSlug='hr-employees/work-locations'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormWorkLocation {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableWorkLocations;
