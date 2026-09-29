import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrEmploymentType from './FormHrEmploymentType'

const componentName = 'TableHrEmploymentType'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableHrEmploymentType = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  baseUrlSlug='hr-recruitment/employment-types'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormHrEmploymentType {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableHrEmploymentType