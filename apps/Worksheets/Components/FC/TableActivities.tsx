import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormActivity, { FormActivityProps } from './FormActivity';

interface TableActivitiesProps extends TableProps {
  idTask?: number,
  idProject?: number,
  idOrder?: number,
}

const componentName = 'TableActivities'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Worksheets';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableActivities = (props: TableActivitiesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Activity'}
    endpointParams={{idTask: props.idTask, idProject: props.idProject, idOrder: props.idOrder}}
    formUrlSlug='worksheets'
    formModalProps={{type: 'centered theme-secondary'}}
    formDefaultValues={{id_task: props.idTask}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    // renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => { return table.renderDefaultCell(columnName, column, data, options); }}
    // renderActionsColumn={(table: TableMeta, row: any) => { return table.renderDefaultActionsColumn(row); }}
    renderFooter={(table: TableMeta) => {
      console.log('renderfooter');
      let workedTotal = 0;
      for (let i in table.data?.records) {
        const row = table.data?.records[i];
        if (row['total_worked_hours']) workedTotal += parseFloat(row['total_worked_hours']);
        else workedTotal += parseFloat(row['worked_hours']);
      }

      return <div className="font-bold bg-yellow-50 p-2">
        {T.translate('Worked total')}: {globalThis.hubleto.numberFormat(workedTotal, 2, ",", " ")} {T.translate('hours')}<br/>
      </div>;
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormActivity {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableActivities;
