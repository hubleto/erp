import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormItem from './FormItem';
import request from '@hubleto/react-ui/core/Request';

interface TableItemsProps extends TableProps {
  idInvoice?: number,
}

const componentName = 'TableItems'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableItems = (props: TableItemsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Item'}
    endpointParams={{idInvoice: props.idInvoice}}
    formUrlSlug='invoices/items'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_invoice: props.idInvoice}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "id_invoice" && !data['INVOICE']) {
        return <button
          className='btn btn-yellow btn-small'
          onClick={() => {
            return globalThis.hubleto.showDialogConfirm(
              <>
                <div className='font-bold'>{data.CUSTOMER?.name}</div>
                <div className='font-bold'>{data.item}</div>
                <div className='font-bold'>{data.unit_price} € x {data.amount} pcs</div>
                <div className='mt-4'>{T.translate('Create invoice from this item? Press OK to confirm.')}</div>
              </>,
              {
                headerClassName: 'dialog-warning-header',
                contentClassName: 'dialog-warning-content',
                header: T.translate('Create invoice'),
                yesText: T.translate('Yes, create invoice'),
                yesButtonClass: 'btn-warning',
                onYes: () => {
                  request.post('invoices/api/create-invoice-from-prepared-item',
                    {
                      idItem: data['id']
                    },
                    {},
                    (data: any) => {
                      if (!isNaN(data.idInvoice)) {
                        window.location.href = globalThis.hubleto.config.projectUrl + '/invoices/' + data.idInvoice;
                      }
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
          <span className='text'>{T.translate('Create invoice')}</span>
        </button>;
      } else {
          return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    // renderActionsColumn={(table: TableMeta, row: any) => { return table.renderDefaultActionsColumn(row); }}
    renderFooter={(table: TableMeta) => {
      let totalExclVat = 0;
      let totalVat = 0;
      let totalInclVat = 0;

      for (let i in table.data.records) {
        const row = table.data.records[i];
        totalExclVat += parseFloat(row['price_excl_vat']);
        totalVat += parseFloat(row['price_incl_vat']) - parseFloat(row['price_excl_vat']);
        totalInclVat += parseFloat(row['price_incl_vat']);
      }

      return <div className="font-bold">
        {T.translate('Total excl. VAT')}: {totalExclVat.toFixed(2)} €<br/>
        {T.translate('Total VAT')}: {totalVat.toFixed(2)} €<br/>
        {T.translate('Total incl. VAT')}: {totalInclVat.toFixed(2)} €
      </div>
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormItem {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableItems;
