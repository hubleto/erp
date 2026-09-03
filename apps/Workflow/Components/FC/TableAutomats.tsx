import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormAutomat from './FormAutomat';

interface TableAutomatsProps extends TableProps {}

const componentName = 'TableAutomats'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableAutomats = (props: TableAutomatsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Automat'}
    formUrlSlug='workflow/automats'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormAutomat {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableAutomats;
