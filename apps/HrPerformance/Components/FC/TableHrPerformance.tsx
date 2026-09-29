import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrCourse from './FormHrCourse'
import FormHrGoal from './FormHrGoal'
import FormHrLearningAssignment from './FormHrLearningAssignment'
import FormHrReview from './FormHrReview'

interface TableHrPerformanceProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrPerformance';
const parentApp = 'Hubleto/App/Community/HrPerformance';

const TableHrPerformance = (props: TableHrPerformanceProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => {
    const formProps = table.getDefaultFormProps();
    switch (props.model) {
      case parentApp + '/Models/Review': return <FormHrReview {...formProps} />;
      case parentApp + '/Models/Course': return <FormHrCourse {...formProps} />;
      case parentApp + '/Models/LearningAssignment': return <FormHrLearningAssignment {...formProps} />;
      default: return <FormHrGoal {...formProps} />;
    }
  }}
  {...props}
/>

export default TableHrPerformance;