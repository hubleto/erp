import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMilestoneTask, { FormMilestoneTaskProps } from './FormMilestoneTask';

interface TableMilestoneTasksProps extends TableProps {
  idMilestone?: number,
}

const componentName = 'TableMilestoneTasks'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';

const TableMilestoneTasks = (props: TableMilestoneTasksProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/MilestoneTask'}
    endpointParams={{idMilestone: props.idMilestone}}
    formUrlSlug='projects/milestones/tasks'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_milestone: props.idMilestone}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMilestoneTask {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMilestoneTasks;
