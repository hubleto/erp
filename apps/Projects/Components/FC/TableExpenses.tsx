import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormExpense, { FormExpenseProps } from './FormExpense';

interface TableExpensesProps extends TableProps {
  idProject?: number,
}

const componentName = 'TableExpenses'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableExpenses = (props: TableExpensesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Expense'}
    endpointParams={{idProject: props.idProject}}
    formUrlSlug='projects/expenses'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_project: props.idProject}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormExpense {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableExpenses;
