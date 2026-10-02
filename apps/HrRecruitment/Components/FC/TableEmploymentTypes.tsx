import Table from '@hubleto/react-ui/components/fc/Table'
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormEmploymentType from './FormEmploymentType'

const componentName = 'TableEmploymentTypes'
const parentApp = 'Hubleto/App/Community/HrRecruitment'

const TableEmploymentTypes = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  baseUrlSlug='hr-recruitment/employment-types'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormEmploymentType {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableEmploymentTypes;