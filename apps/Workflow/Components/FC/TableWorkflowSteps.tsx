import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormWorkflowStep, { FormWorkflowStepProps } from './FormWorkflowStep';

interface TableWorkflowStepsProps extends TableProps {
  idWorkflow?: number,
}

const componentName = 'TableWorkflowSteps'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';

const TableWorkflowSteps = (props: TableWorkflowStepsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/WorkflowStep'}
    endpointParams={{idWorkflow: props.idWorkflow}}
    formUrlSlug='workflow/steps'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_workflow: props.idWorkflow}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormWorkflowStep {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableWorkflowSteps;
