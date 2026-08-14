import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMilestone, { FormMilestoneProps } from './FormMilestone';

interface TableMilestonesProps extends TableProps {
  idProject?: number,
}

const componentName = 'TableMilestones'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';

const TableMilestones = (props: TableMilestonesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Milestone'}
    endpointParams={{idProject: props.idProject}}
    formUrlSlug='projects/milestones'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_project: props.idProject}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMilestone {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMilestones;
