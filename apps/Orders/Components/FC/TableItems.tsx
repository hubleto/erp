import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormItem from './FormItem';
import moment from "moment";
import request from '@hubleto/react-ui/core/Request';

interface TableItemsProps extends TableProps {
  idOrder?: number,
}

const componentName = 'TableItems'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableItems = (props: TableItemsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Item'}
    endpointParams={{idOrder: props.idOrder}}
    formUrlSlug='orders/items'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_order: props.idOrder}}
    getCellClassName={(table: TableMeta, columnName: string, column: any, rowData: any): string => {
      const cellClassName = table.getDefaultCellClassName(columnName, column, rowData);
      if (columnName == 'date_due') {
        const now = moment();
        const daysDue = moment(now).diff(moment(rowData['date_due']), 'days');
        if (!rowData['INVOICE_ITEM']) {
          if (daysDue >= 0) return cellClassName + ' bg-red-200 text-red-800';
          else if (daysDue > -7) return cellClassName + ' text-yellow-800';
          else return cellClassName;
        } else {
          return cellClassName;
        }
      } else {
        return cellClassName;
      }
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "id_invoice_item") {
        if (data['INVOICE_ITEM']) {
          return table.renderDefaultCell(columnName, column, data, options);
        } else if (data['is_chargeable']) {
          return <button
            className='btn btn-yellow btn-small'
            onClick={(e) => {
              console.log('ggg');
              e.stopPropagation();
              globalThis.hubleto.showDialogConfirm(
                <>
                  <div>{data.ORDER?.identifier} ({data.ORDER?.title})</div>
                  <div className='font-bold'>{data.title}</div>
                  <div className='font-bold'>{data.unit_price} € x {data.amount} pcs</div>
                  <div className='mt-4'>{T.translate('Is this item ready to be invoiced? Press OK to confirm.')}</div>
                </>,
                {
                  headerClassName: 'dialog-warning-header',
                  contentClassName: 'dialog-warning-content',
                  header: T.translate('Prepare for invoice'),
                  yesText: T.translate('Yes, prepare for invoice'),
                  yesButtonClass: 'btn-warning',
                  onYes: () => {
                    request.get('orders/api/prepare-item-for-invoice',
                      {
                        idOrder: data['id_order'],
                        idItem: data['id'],
                      },
                      (data: any) => {
                        table.loadData();
                      }
                    );
                  },
                  noText: T.translate('Cancel'),
                  noButtonClass: 'btn-transparent',
                  onNo: () => {
                  },
                  onHide: () => {
                  },
                }
              );
            }}
          >
            <span className='icon'><i className='fas fa-euro-sign'></i></span>
            <span className='text'>{T.translate('Prepare for invoice')}</span>
          </button>;
        }
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormItem {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableItems;
