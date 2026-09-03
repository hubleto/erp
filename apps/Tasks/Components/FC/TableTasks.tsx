import React, { useState } from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTask, { FormTaskProps } from './FormTask';
import FormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarFormActivity';

const componentName = 'TableTasks'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Tasks';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableTasks = (props: TableProps) => {
  const [addActivityForIdTask, setAddActivityForIdTask] = useState(0);

  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Task'}
    formUrlSlug='tasks'
    formModalProps={{type: 'right wide'}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "title") {
        return <>
          {table.renderDefaultCell(columnName, column, data, options)}
          {data['TODO'] ? data['TODO'].map((item, key) => {
            if (item.is_closed) return null;
            else return <div className='text-yellow-600 font-normal text-xs' key={key}>{item.todo}</div>
          }) : null}
        </>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderActionsColumn={(table: TableMeta, row: any) => {
      return <>
        <button
          className="btn btn-small btn-add-outline text-nowrap"
          onClick={(e) => {
            e.preventDefault();
            setAddActivityForIdTask(row.id);
          }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
          <span className="text">{T.translate('Add activity')}</span>
        </button>
      </>;
    }}
    // renderFooter={(table: TableMeta) => { return table.renderDefaultFooter(); }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <>
        <FormTask {...table.getDefaultFormProps()}/>
        {addActivityForIdTask > 0 ?
          <FormActivity
            id={-1}
            description={{defaultValues: {id_task: addActivityForIdTask}}}
            onClose={() => {
              setAddActivityForIdTask(0);
              table.reload();
            }}
          ></FormActivity>
        : null}
      </>
    }}
    {...props}
  ></Table>
}

export default TableTasks;
