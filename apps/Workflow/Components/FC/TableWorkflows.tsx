import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormWorkflow, { FormWorkflowProps } from './FormWorkflow';

interface TableWorkflowsProps extends TableProps {}

const componentName = 'TableWorkflows'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableWorkflows = (props: TableWorkflowsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Workflow'}
    formUrlSlug='workflow/workflows'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormWorkflow {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableWorkflows;
