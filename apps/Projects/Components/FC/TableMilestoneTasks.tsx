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
    baseUrlSlug='projects/milestones/tasks'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_milestone: props.idMilestone}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "id_task") {
        const TASK = data.TASK ?? {};
        return <>
          <div>{TASK.identifier} {TASK.title}</div>
          {TASK.is_closed ? <div className="badge badge-danger">Closed</div> : null}
        </>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMilestoneTask {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMilestoneTasks;
