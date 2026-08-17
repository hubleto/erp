import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormInvoice, { FormInvoiceProps } from './FormInvoice';
import moment from "moment";

const componentName = 'TableInvoices'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableInvoices = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Invoice'}
    formUrlSlug='invoices'
    formModalProps={{type: 'right wide'}}
    getCellClassName={(table: TableMeta, columnName: string, column: any, rowData: any): string => {
      const cellClassName = table.getDefaultRowClassName(rowData);

      if (columnName == 'date_due') {
        const now = moment();
        const daysDue = moment(now).diff(moment(rowData['date_due']), 'days');
        const datePayment = moment(rowData['date_payment']);

        if (!datePayment.isValid()) {
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
      if (columnName == "date_sent" && !data['date_sent']) {
        return <div className='badge badge-danger'>{T.translate('Not sent')}</div>;
      } else if (columnName == "date_payment" && !data['date_payment']) {
        const now = moment();
        const dateDue = moment(data['date_due']);
        const badgeColor = (now > dateDue ? 'danger' : 'warning');
        return <div className={'badge badge-' + badgeColor}>{T.translate('Not paid')}</div>;
      } else if (columnName == "virt_items") {
        try {
          let items = JSON.parse(data['virt_items']);
          return <div className='flex flex-col'>{items.map((item, index) => {
            return <div key={index} className='badge text-xs'>
              {item.item}: {item.amount ?? 0} ks x {globalThis.hubleto.currencyFormat(item.unit_price)} €
            </div>
          })}</div>;
        } catch (ex) {
          return null;
        }
      } else if (columnName == "virt_payments") {
        try {
          let payments = JSON.parse(data['virt_payments']);
          return <div className='flex flex-col'>{payments.map((payment, index) => {
            return <div key={index} className='badge text-xs'>
              {payment.date_payment} {payment.amount ?? 0} €
            </div>
          })}</div>;
        } catch (ex) {
          return null;
        }
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderFooter={(table: TableMeta) => {
      let totalExclVat = 0;
      let totalVat = 0;
      let totalInclVat = 0;

      for (let i in table.data?.records) {
        const row = table.data?.records[i];
        totalExclVat += parseFloat(row['total_excl_vat']);
        totalVat += parseFloat(row['total_incl_vat']) - parseFloat(row['total_excl_vat']);
        totalInclVat += parseFloat(row['total_incl_vat']);
      }

      return <div className="font-bold">
        {T.translate('Total excl. VAT')}: {totalExclVat.toFixed(2)} €<br/>
        {T.translate('Total VAT')}: {totalVat.toFixed(2)} €<br/>
        {T.translate('Total incl. VAT')}: {totalInclVat.toFixed(2)} €
      </div>;
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormInvoice {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableInvoices;
