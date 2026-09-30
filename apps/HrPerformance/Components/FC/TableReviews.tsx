import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormReview from './FormReview'

const componentName = 'TableReviews';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableReviews = (props: TableProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Review'}
  baseUrlSlug='hr-performance/reviews'
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => <FormReview {...table.getDefaultFormProps()} />}
  {...props}
/>

export default TableReviews;