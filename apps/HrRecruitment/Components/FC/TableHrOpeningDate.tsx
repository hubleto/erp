import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrOpeningDate from './FormHrOpeningDate'

const componentName = 'TableHrOpeningDate'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableHrOpeningDate = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/OpeningDate'}
  baseUrlSlug='hr-recruitment/opening-dates'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrOpeningDate {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrOpeningDate