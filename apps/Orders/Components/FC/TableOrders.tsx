import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormOrder, { FormOrderProps } from './FormOrder';

interface TableOrdersProps extends TableProps {}

const componentName = 'TableOrders'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableOrders = (props: TableOrdersProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Order'}
    formUrlSlug='orders'
    formModalProps={{type: 'right wide'}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
    return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_last_item") {
        if (data.virt_last_item) {
          let lastItem: any = {};
          try { lastItem = JSON.parse(data.virt_last_item); } catch (ex) { }
          return <div className='flex gap-1 text-xs items-center'>
            {lastItem.date_due}
            <div className='badge'>{lastItem.title}</div> {globalThis.hubleto.currencyFormat(lastItem.unit_price, 2)} x {lastItem.amount}
          </div>;
        } else {
          return null;
        }
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormOrder {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableOrders;
