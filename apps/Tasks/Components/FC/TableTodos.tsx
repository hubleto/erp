import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTodo, { FormTodoProps } from './FormTodo';

const componentName = 'TableTodos'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Tasks';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableTodos = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Todo'}
    formUrlSlug='tasks/todo'
    formModalProps={{type: 'centered small'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormTodo {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableTodos;
