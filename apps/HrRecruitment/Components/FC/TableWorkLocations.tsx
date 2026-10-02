import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormWorkLocation from './FormWorkLocation'

const componentName = 'TableWorkLocation'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableWorkLocations = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/WorkLocation'}
  baseUrlSlug='hr-recruitment/work-locations'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormWorkLocation {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableWorkLocations;