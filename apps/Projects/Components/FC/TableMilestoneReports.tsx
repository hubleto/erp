import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMilestoneReport, { FormMilestoneReportProps } from './FormMilestoneReport';

interface TableMilestoneReportsProps extends TableProps {
  idMilestone?: number,
}

const componentName = 'TableMilestoneReports'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableMilestoneReports = (props: TableMilestoneReportsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/MilestoneReport'}
    endpointParams={{idMilestone: props.idMilestone}}
    formUrlSlug='projects/milestones/reports'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_milestone: props.idMilestone}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMilestoneReport {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMilestoneReports;
