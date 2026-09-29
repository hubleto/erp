import Table from '@hubleto/react-ui/components/fc/Table'
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces'
import FormHrApplication from './FormHrApplication'
import FormHrCandidate from './FormHrCandidate'
import FormHrInterview from './FormHrInterview'
import FormHrJobOpening from './FormHrJobOpening'

interface TableHrRecruitmentProps extends TableProps {
  model: string;
  baseUrlSlug: string;
}

const componentName = 'TableHrRecruitment';
const parentApp = 'Hubleto/App/Community/HrRecruitment';

const TableHrRecruitment = (props: TableHrRecruitmentProps) => <Table
  componentName={componentName}
  parentApp={parentApp}
  model={props.model}
  baseUrlSlug={props.baseUrlSlug}
  formModalProps={{type: 'right wide'}}
  renderForm={(table: TableMeta) => {
    const formProps = table.getDefaultFormProps();
    switch (props.model) {
      case parentApp + '/Models/Candidate': return <FormHrCandidate {...formProps} />;
      case parentApp + '/Models/Application': return <FormHrApplication {...formProps} />;
      case parentApp + '/Models/Interview': return <FormHrInterview {...formProps} />;
      default: return <FormHrJobOpening {...formProps} />;
    }
  }}
  {...props}
/>

export default TableHrRecruitment;